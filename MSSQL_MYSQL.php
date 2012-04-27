<?php
// Для корректной конвертации дат из MSSQL надо поменять настройку в php.ini:
// ; Specify how datetime and datetim4 columns are returned
// ; On => Returns data converted to SQL server settings
// ; Off => Returns values as YYYY-MM-DD hh:mm:ss
// mssql.datetimeconvert = Off


/**
 * Данные из MSSQL об схеме БД (одной колонке)
 */
class SQL_Column
{
	/**
	 * @var string Название таблицы
	 */
	var $TABLE_NAME;

	/**
	 * @var string Название колонки
	 */
	var $COLUMN_NAME;

	/**
	 * @var string Обычно равен имени БД
	 */
	var $TABLE_CATALOG;

	/**
	 * @var string обычно 'dbo' DataBaseObject
	 */
	var $TABLE_SCHEMA;

	/**
	 * @var int Номер колонки (начиная с 1)
	 */
	var $ORDINAL_POSITION;

	/**
	 * @var string Может ли быть NULL (YES/NO)
	 */
	var $IS_NULLABLE;

	/**
	 * @var string Тип в БД MSSQL
	 */
	var $DATA_TYPE;

	/**
	 * @var int Максимальная длина в символах
	 */
	var $CHARACTER_MAXIMUM_LENGTH;
	var $CHARACTER_OCTET_LENGTH;
	var $NUMERIC_PRECISION;
	var $NUMERIC_PRECISION_RADIX;
	var $NUMERIC_SCALE;
	var $DATETIME_PRECISION;
	var $CHARACTER_SET_CATALOG;
	var $CHARACTER_SET_SCHEMA;
	var $CHARACTER_SET_NAME; //cp1251
	var $COLLATION_CATALOG; //
	var $COLLATION_SCHEMA; //
	var $COLLATION_NAME; //Cyrillic_General_CI_AS
	var $DOMAIN_CATALOG; //
	var $DOMAIN_SCHEMA; //
	var $DOMAIN_NAME; //

	/**
	 * @var string Значение колонки по-умолчанию
	 */
	public $COLUMN_DEFAULT;

	/**
	 * @var boolean Является ли первичным ключом?
	 */
	var $isPrimaryKey;

	/**
	 * @var Описание (Description)
	 */
	public $comment;

	/**
	 * @var Поле - автоматически увеличивающийся счётчик (автоинкремент)
	 */
	public $autoincrement = false;
}

/**
 * Таблица в БД
 */
class SQL_Table
{
	/**
	 * @var array|SQL_Column
	 */
	public $columns = array();

	/**
	 * @var array|string Имена полей - первичного ключа таблицы (пока поддерживается только один первичный ключ в
	 * таблице)
	 */
	public $primaryKey = array();

	/**
	 * @var string Название таблицы
	 */
	public $name;

	/**
	 * @var bool Является ли представлением
	 */
	public $view = null;

	/**
	 * @return string Начало оператора CREATE для создания таблицы в БД
	 */
	public function Create_start()
	{
		return "CREATE TABLE $this->name(";
	}

	/**
	 * @param  $column_no
	 * @return string SQL-запрос для создания колонки column_no
	 */
	public function Create($column_no)
	{
		return $this->Column_SQL($this->columns[$column_no]);
	}

	/**
	 * @param  $column SQL_Column
	 * @return string
	 */
	public function Column_SQL($column)
	{
		/** @var $column SQL_Column */
		$this->type = $this->MapDataType($column);

		$default = $this->MapDefault(trim($column->COLUMN_DEFAULT));

		$res = $column->COLUMN_NAME . " " . $this->type;
		if ($column->IS_NULLABLE === 'NO') $res .= " NOT NULL";
		if ($column->autoincrement)
			$res .= ' AUTO_INCREMENT';
		if (!empty($column->comment))
			$res .= " COMMENT '" . $column->comment . "'";
		$res .= $default;
		return $res;
	}

	public function MapDefault($default)
	{
		if ($this->type == "BOOL")
			return '';
		if (strpos($default, "('") === 0)
			return " DEFAULT '" . substr($default, 2, strlen($default) - 4) . "'";
		if (strpos($default, "((") === 0)
			return " DEFAULT " . substr($default, 2, strlen($default) - 4);
		switch ($default) {
			case "": /* Ничего делать не надо */
				return '';
				break;
			case "(getdate())":
				$this->type = "TIMESTAMP";
				return " DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";
			default:
				throw new Exception("case \"" . $default . "\": \$res .= \"" . $default . "\"; break;");
		}
	}

	/**
	 * Перевод типов данных MSSQL в типы данных MySQL
	 * @throws Exception
	 * @param  $column SQL_Column
	 * @return string
	 */
	private function MapDataType($column)
	{
		switch ($column->DATA_TYPE) {
			case 'int':
				return 'INT(11)';
			case "char":
				return "CHAR(1)";
			case "varchar":
			case "nvarchar":
				return "VarChar(" . $column->CHARACTER_MAXIMUM_LENGTH . ")";
			case 'datetime':
				return 'DateTime';
			case 'text':
				return 'TEXT';
			case 'date':
				return 'DATE';
			case "float":
				return $column->DATA_TYPE;
			case "bit":
				return 'BOOL';
			default:
				throw new Exception($column->DATA_TYPE);
		}
	}

	/**
	 * @return string SQL для создания ключевого поля
	 */
	public function Create_primary_key()
	{
		if (empty($this->primaryKey))
			return '';
		else {
			$first = true;
			$s = '';
			foreach ($this->primaryKey as $key) {
				if ($first)
					$first = false;
				else
					$s .= ', ';
				$s .= $key;
			}
			return "PRIMARY KEY (" . $s . ")";
		}
	}

	/**
	 * @return string Окончание CREATE для создания таблицы
	 */
	public function Create_end()
	{
		return ") ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
	}

	/**
	 * @return string SQL-запрос для удаления этой таблицы
	 */
	public function Drop_Table()
	{
		return "DROP TABLE IF EXISTS $this->name;";
	}

	/**
	 * @return string SQL-запрос для создания таблицы
	 */
	public function Create_table()
	{
		if (!empty($this->view)) {
			$sql = $this->view;
			$sql = str_replace('  ', ' ', $sql);
			$sql = str_replace('  ', ' ', $sql);
			$sql = str_replace('  ', ' ', $sql);
			$sql = str_replace('  ', ' ', $sql);
			$sql = str_replace('dbo.', '', $sql);
			$sql = str_replace('TOP (100) PERCENT', '', $sql);
			$sql = str_replace('[user]', 'user', $sql);
			$sql = str_replace('[dbo].', '', $sql);
			$sql = str_replace('[zanesenie_view]', 'zanesenie_view', $sql);
			return $sql;
		}
		$res = $this->Create_start() . "\n\r";
		$first = true;
		foreach ($this->columns as $column) {
			if ($first)
				$first = false;
			else
				$res .= ",\n\r";
			$res .= $this->Column_SQL($column);
		}
		$s = $this->Create_primary_key();
		if (!empty($s)) {
			$res .= ",\n\r" . $s . "\n\r";
		} else
			$res .= "\n\r";
		$res .= $this->Create_end();
		return $res;
	}
}

/**
 * Конвертер структуры из БД MSSQL в MySQL
 */
class MSSQL_MYSQL
{
	/**
	 * @var Соединение с БД MSSQL
	 */
	public $mssql;

	/**
	 * @var Соединение с БД MySQL
	 */
	public $mysql;

	/**
	 * @var array|SQL_Table Массив SQL таблиц
	 */
	public $tables = array();

	/**
	 * Получение схемы БД из MSSQL
	 * @return void
	 */
	public function GetSchema()
	{
		$res = mssql_query("SELECT * FROM INFORMATION_SCHEMA.COLUMNS ORDER BY TABLE_NAME,
    ORDINAL_POSITION", $this->mssql);

		while ($column = mssql_fetch_object($res)) {
			/** @var $f SQL_Column */
			if (!isset($this->tables[$column->TABLE_NAME])) {
				$this->tables[$column->TABLE_NAME] = new SQL_Table();
			}
			$table = &$this->tables[$column->TABLE_NAME];
			/** @var $name SQL_Table */
			$table->name = $column->TABLE_NAME;
			$column->isPrimaryKey = false;
			$column->autoincrement = false;
			$column->COLUMN_DEFAULT = $this->encoding($column->COLUMN_DEFAULT);

			$table->columns[] = $column;
		}

		// Выясняем, какие поля являются ключевыми
		$res2 = mssql_query("SELECT TABLE_NAME,COLUMN_NAME,ORDINAL_POSITION FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
   WHERE CONSTRAINT_NAME in
    (SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE CONSTRAINT_TYPE =
    'PRIMARY KEY')",
			$this->mssql);
		while ($c = mssql_fetch_array($res2)) {
			$table = &$this->tables[$c['TABLE_NAME']];
			$table->primaryKey[] = $c['COLUMN_NAME'];
			foreach ($table->columns as $column) {
				if ($column->COLUMN_NAME == $c['COLUMN_NAME'])
					$column->isPrimaryKey = true;
			}
		}

		// Ищем поля с автоинкрементом
		$res_auto = mssql_query("SELECT O.name AS TableName, C.name AS ColumnName, C.seed_value, C.increment_value FROM
      sys.identity_columns AS C LEFT JOIN sys.all_objects AS O ON C.object_id = O.object_id
      WHERE C.user_type_id = 56"); // Почему 56? Не знаю, но все пользовательские стоблцы с таким user_type_id
		while ($c = mssql_fetch_array($res_auto)) {
			$table = &$this->tables[$c['TableName']];
			foreach ($table->columns as $column) {
				if ($column->COLUMN_NAME == $c['ColumnName'])
					$column->autoincrement = true;
			}
		}

		// Убираем системные таблицы
		// TODO: Сделать это правильно (анализируя какие-нибудь атрибуты)
		unset($this->tables['dtproperties']);
		unset($this->tables['sysdiagrams']);

		// Переделываем View
		$res_view = mssql_query("SELECT TABLE_NAME,convert(text,VIEW_DEFINITION) AS V FROM INFORMATION_SCHEMA.VIEWS");
		while ($c = mssql_fetch_array($res_view)) {
			$table = &$this->tables[$c['TABLE_NAME']];
			$table->view = $c['V'];
		}

		// Получаем комментарии к колонкам по всем таблицам
		foreach ($this->tables as $table) {
			// За один запрос можно получить Описания к колонкам только для одной таблицы
			$res3 = mssql_query("SELECT * FROM ::fn_listextendedproperty (NULL, 'user', 'dbo', 'table', '$table->name',
      'column', default)");
			while ($c = mssql_fetch_array($res3)) {
				foreach ($table->columns as $column)
					if ($column->COLUMN_NAME == $c['objname']) {
						$column->comment = trim($this->encoding($c['value']));
						break;
					}
			}
		}
		// foreach($this->tables as $table) echo " ".$table->name; // Для отладки
	}

	/**
	 * @param  $host
	 * @param  $mysql_user
	 * @param  $mysql_password
	 * @param  $datebaseName
	 *
	 * @internal param $dbc
	 * @return void
	 */
	public function RecreateDatabase($host, $mysql_user, $mysql_password, $datebaseName)
	{
		$this->mysql = mysql_connect($host, $mysql_user, $mysql_password);
		mysql_query("SET NAMES utf8");
		mysql_query('DROP DATABASE IF EXISTS ' . $datebaseName . ';', $this->mysql) or die(mysql_error($this->mysql));
		mysql_query('CREATE DATABASE ' . $datebaseName . ' CHARACTER SET utf8 COLLATE utf8_general_ci;', $this->mysql) or die(mysql_error($this->mysql));
		mysql_select_db($datebaseName, $this->mysql) or die(mysql_error($this->mysql));
	}

	/**
	 * @param  $table SQL_Table Таблица, в которую будем заносить значения
	 * @param  $row array Строка значений из БД MSSQL
	 * @return void
	 */
	public function insertRow($table, $row)
	{
		$sql = "INSERT INTO " . $table->name . " VALUES\n\r  (";
		$debug = "";
		$first = true;
		foreach ($row as $key => $value) {
			// Если это не первая колонка, то перед значением надо поставить запятую и пробел
			if ($first) $first = false; else $sql .= ', ';
			// Если это строка, то нужно окружить апострофами
			if (is_string($value)) {
				$sql .= "'" . $this->encoding($value) . "'";
			} elseif ($value === null) { // Отдельно надо обработать значение NULL
				$sql .= 'NULL';
			} else
				$sql .= $value; // Это (скорее всего) число :)
			$debug .= "$key => $value  ";
		}
		$sql .= ");";
		// Пытаемся занести в БД MySQL, если не получается, то выводим сообщение об ошибке и запрос
		mysql_query($sql, $this->mysql) or die(mysql_error($this->mysql) . "\r\n" . $sql . "\r\n" . $debug);
	}

	/**
	 * Меняем кодировку с той что была в MSSQL на ту что будет в MySQL
	 * @param  $value
	 * @return string
	 */
	public function encoding($value)
	{
		return iconv("Windows-1251", "UTF-8", $value);
	}
}
