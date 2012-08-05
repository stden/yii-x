<?php
/**
 * Утилиты для отладки и генерации Unit-тестов на основе данных пользователя
 */
class Debug
{
	/**
	 * Генерируем инициализацию переменной для модульных тестов.
	 * @static
	 * @param $var_name Имя переменной
	 * @param $value Значение переменной
	 * @return string Строка инициализации
	 */
	public static function Init($var_name, $value)
	{
		return "\${$var_name} = " . var_export($value, true) . ";\n";
	}

	/**
	 * Генерируем assertEquals для всех полей объекта.
	 * @static
	 * @param $var_name
	 * @param $value
	 * @return string
	 */
	public static function Asserts($var_name, $value)
	{
		if (is_null($value))
			return "\$this->assertNull($var_name);\n";
		if (is_int($value) || is_double($value))
			return "\$this->assertEquals($value, $var_name);\n";
		if (is_string($value))
			return "\$this->assertEquals(" . Debug::value($value) . ", $var_name);\n";
		if (is_bool($value))
			if ($value) {
				return "\$this->assertTrue($var_name);\n";
			} else {
				return "\$this->assertFalse($var_name);\n";
			}
		if (is_array($value)) {
			return "\$this->assertEquals(" . Debug::value($value) . ", $var_name);\n";
		}

		if (is_subclass_of($value, 'CActiveRecord')) {
			$res = "";
			foreach ($value->attributes as $k => $v) {
				if ($k == 'modified') continue; // Игнорируем такие поля
				$res .= self::genAssert("$var_name->$k", $v);
			}
			return $res;
		}
		if (is_object($value)) {
			$res = "";
			foreach (get_object_vars($value) as $k => $v) {
				$res .= self::genAssert("$var_name->$k", $v);
			}
			return $res;
		}
		throw new Exception($value);
	}

	/**
	 * @static Генерация нужного assert для поля
	 * @param $k
	 * @param $v
	 * @return string
	 */
	public static function genAssert($k, $v)
	{
		if (is_null($v)) {
			return "\$this->assertNull($k);\n";
		} else {
			return "\$this->assertEquals(" . var_export($v, true) . ",$k);\n";
		}
	}

	/**
	 * @static Замена символов для модульных тестов
	 * @param $s значение 
	 * @return string
	 */
	public static function value($s)
	{
		if (is_int($s) || is_double($s))
			return $s;
		if (is_array($s)) {
			$r = '[';
			$first = true;
			$cnt = 0; // Считаем какой сейчас элемент массива
			foreach ($s as $k => $v) {
				if ($first)
					$first = false;
				else
					$r .= ', ';
				if ($k != $cnt) { // Если ключ не совпадает с номером элемента
					$r .= Debug::value($k) . ' => ';
					if (is_int($k))
						$cnt = $k;
				}
				$r .= Debug::value($v);
				if (is_int($k))
					$cnt++;
			}
			$r .= ']';
			return $r;
		}
		if (is_string($s)) {
			// Экранируем служебные символы
			$s = str_replace("\n", '\n', $s);
			$s = str_replace("\t", '\t', $s);
			return '"' . $s . '"';
		}
		return var_export($s, true);
	}
}
