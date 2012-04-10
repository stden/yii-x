<?php
/**
 * Пересоздание тестовой БД
 */
class MySQLReload
{

	/**
	 * @var string Сервер
	 */
	public $server = 'localhost';

	/**
	 * @var string Имя пользователя
	 */
	public $username = 'root';

	/**
	 * @var string Пароль пользователя
	 */
	public $password = '';

	/**
	 * @var string Название БД
	 */
	public $db_name;

	/**
	 * @var string Кодировка
	 */
	public $charset = 'utf8';

	/**
	 * @var string Метод сортировки строк
	 */
	public $collation = 'utf8_general_ci';

	/**
	 * Пересоздание базы данных (удаление и создание заново)
	 * @return void
	 */
	public function recreateDatabase()
	{
		$link = mysql_connect($this->server, $this->username, $this->password);
		mysql_query("SET NAMES $this->charset", $link);
		mysql_query("DROP DATABASE IF EXISTS $this->db_name", $link) or die(mysql_error($link));
		mysql_query(
			"CREATE DATABASE $this->db_name CHARACTER SET $this->charset COLLATE $this->collation", $link)
			or die(mysql_error($link));
		mysql_close($link);
	}

	/**
	 * Импорт MySQL дампа
	 * @param $filename Имя SQL файла для импорта
	 */
	public function import($filename)
	{
		$link = mysql_connect($this->server, $this->username, $this->password);
		mysql_query("SET NAMES $this->charset", $link);
		mysql_select_db($this->db_name, $link) or die(mysql_error($link));

		$query = ''; // В этой строке накапливается многострочный запрос
		// Читаем весь файл в массив
		$lines = file($filename);
		// Цикл по строкам файла
		foreach ($lines as $line)
		{
			// Если это комментарий или пустая строка, пропускаем её
			if (substr($line, 0, 2) == '--' || $line == '') {
				continue;
			}
			// Добавляем новую строку к текущему запросу
			$query .= $line;
			// Если есть точка с запятой в конце строки, то это конец запроса
			if (substr(trim($line), -1, 1) == ';') {
				// Выполняем запрос
				mysql_query($query) or die('Error \'' . $query . '\': ' . mysql_error());
				// Очищаем запрос
				$query = '';
			}
		}
		mysql_close($link);
	}
}
