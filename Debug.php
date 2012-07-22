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
     * @param CActiveRecord $value
     * @return string
     */
    public static function Asserts($var_name, $value)
    {
        if (is_null($value))
            return "\$this->assertNull($var_name);\n";
        $res = "";
        if (is_array($value))
            foreach ($value as $k => $v) {
                $res .= "\$this->assertEquals(" . var_export($v, true) . ",{$var_name}['{$k}']);\n";
            }
        if ($value instanceof CActiveRecord)
            foreach ($value->attributes as $k => $v) {
                $res .= "\$this->assertEquals(" . var_export($v, true) . ",$var_name->$k);\n";
            }
        return $res;
    }
}
