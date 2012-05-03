<?php
/**
 * Вызов хранимых процедур
 */
class StoredProc
{
	/**
	 * @static Вызов хранимой процедуры с параметрами
	 * @param $sp_name Название хранимой процедуры
	 * @param $params Массив параметров хранимой процедуры
	 * @return array Результаты вызова (массив)
	 */
	public static function Call($sp_name, $params)
	{
		// Получаем CommandBuilder
		$b = Yii::app()->db->getCommandBuilder();
		// Создаём команду
		$pars = implode(',', array_keys($params));
		$cmd = $b->createSqlCommand("CALL $sp_name($pars)", $params);
		// Получаем все записи
		return $cmd->queryAll();
	}
}
