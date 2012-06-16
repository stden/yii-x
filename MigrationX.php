<?php
/**
 * Расширенные миграции (с собственными добавлениями для удобства)
 */
class MigrationX extends CDbMigration
{
    protected $options = NULL;

    public function __construct()
    {
        if (Yii::app()->db->getDriverName() == 'mysql') {
            $this->options = 'engine=InnoDB charset=utf8';
        }
    }

    /**
     * Создает таблицу с двумя полями id и name.
     * @param tableName название таблицы
     * @param hasIndex  bool, если нужно создать индекс по name
     */
    protected function createIdNameTable($tableName, $hasIndex = false)
    {
        if (is_null($tableName)) throw new CException('createManyToManyTable(): tableName is undefined');

        $this->createTable(
            $tableName, [
            'id' => 'pk',
            'name' => 'string not null',
        ], $this->options);

        if ($hasIndex) {
            $this->createIndex("{$tableName}_name", $tableName, 'name');
        }
    }


    /**
     * Создает таблицу с тремя полями id, name, internal и индекс по internal.
     * @param tableName название таблицы
     * @return void
     */
    protected function createIdNameInternalTable($tableName)
    {
        if (is_null($tableName)) throw new CException('createManyToManyTable(): tableName is undefined');

        $this->createTable(
            $tableName, [
            'id' => 'pk',
            'name' => 'string not null',
            'internal' => 'string not null',
        ], $this->options);

        $this->createIndex("{$tableName}_internal", $tableName, 'internal');
    }


    /**
     * Создает many-to-many таблицу.
     * @param $tableName Название новой таблицы
     * @param $desc массив вида:
     *     array(
     *         '_field0_' => array(
     *             'table' => '_ForeignTable_',
     *             'field' => '_ForeignField_', // if omitted 'id' is used
     *         ),
     *     )
     */
    protected function createManyToManyTable($tableName, $desc)
    {
        if (is_null($tableName)) throw new CException('createManyToManyTable(): tableName is undefined');

        $createArray = ['id' => 'pk'];
        $fkArray = [];
        $indexArray = [];

        // construct
        foreach ($desc as $field => $conf) {
            if (!array_key_exists('table', $conf) || is_null($conf['table'])
            ) {
                throw new CException("createManyToManyTable(): table for field '$field' is undefined");
            }

            $createArray[$field] = 'int not null';

            if (!array_key_exists('field', $conf)) $conf['field'] = 'id';
            $fkArray[] =
                "foreign key ($field) references {$conf['table']} ({$conf['field']}) on delete cascade on update cascade";

            $indexArray[] = ["{$tableName}_{$field}", $tableName, $field];
        }
        $createArray = array_merge($createArray, $fkArray);

        // execute
        $this->createTable($tableName, $createArray, $this->options);
        foreach ($indexArray as $idx) {
            $this->createIndex($idx[0], $idx[1], $idx[2]);
        }
    }


    /**
     * Вставляет данные в таблицу id+name.
     * @param $tableName Название таблицы
     * @param $data      Массив со значениями поля name.
     * @return void
     */
    protected function insertIdName($tableName, $data)
    {
        foreach ($data as $value) {
            $this->insert($tableName, ['name' => $value]);
        }
    }

    /**
     * Вставляет данные в таблицу id+name+internal.
     * @param $tableName Название таблицы
     * @param $data      Хэш со значениями поля internal=>name.
     * @return void
     */
    protected function insertIdNameInternal($tableName, $data)
    {
        foreach ($data as $internal => $name) {
            $this->insert($tableName, ['name' => $name, 'internal' => $internal]);
        }
    }

    /**
     * Изменение типа и настроек колонок.
     * @param $table Таблица MySQL
     * @param $column Колонка
     * @param $type Тип
     * @return void
     */
    public function alterColumnX($table, $column, $type)
    {
        // ALTER TABLE ALTER COLUMN умеет только изменять DEFAULT значения
        $this->execute("ALTER TABLE $table CHANGE $column $column $type");
    }
}
