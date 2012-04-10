<?php
/**
 * Различные функции-утилиты для вывода HTML, JavaScript и т.д.
 * Стандарные элементы представления
 */
class HTML
{
    /**
     * Генерирует сетку RadioButton для атрибута модели.
     * Значение атрибута используется как выбранное значение в сетке.
     * @param CModel $model модель
     * @param string $attribute имя атрибута
     * @param array $data массив ключ-значение value=>label для отображения кнопок.
     * @param array $htmlOptions дополнительные HTML опции.
     * @param int $columns во сколько колонок выводить кнопки
     * @return string the generated radio button list
     */
    public static function activeRadioButtonGrid($model, $attribute, $data, $htmlOptions = array(), $columns = 3)
    {
        CHtml::resolveNameID($model, $attribute, $htmlOptions);
        $selection = CHtml::resolveValue($model, $attribute);
        if ($model->hasErrors($attribute)) {
            self::addErrorCss($htmlOptions);
        }
        $name = $htmlOptions['name'];
        unset($htmlOptions['name']);

        if (array_key_exists('uncheckValue', $htmlOptions)) {
            $uncheck = $htmlOptions['uncheckValue'];
            unset($htmlOptions['uncheckValue']);
        }
        else {
            $uncheck = '';
        }

        $hiddenOptions =
            isset($htmlOptions['id']) ? array('id' => CHtml::ID_PREFIX . $htmlOptions['id']) : array('id' => false);
        $hidden = $uncheck !== null ? CHtml::hiddenField($name, $uncheck, $hiddenOptions) : '';

        return $hidden . self::radioButtonGrid($name, $selection, $data, $htmlOptions, $columns);
    }

    /**
     * Генерация сетки (таблицы) RadioButton для атрибута модели.
     * @param string $name название группы RadioButton.
     * @param mixed $select Выбранные RadioButton.
     * @param array $data массив ключ-значение value=>label для отображения кнопок.
     * @param array $htmlOptions дополнительные HTML опции.
     * @param int $columns Количество колонок в сетке (по-умочанию - 3)
     * @return string Набор радиобатонов в виде сетки
     */
    public static function radioButtonGrid($name, $select, $data, $htmlOptions = array(), $columns = 3)
    {
        assert(is_int($columns));

        $template = isset($htmlOptions['template']) ? $htmlOptions['template'] : '{input} {label}';
        unset($htmlOptions['template'], $htmlOptions['separator']);

        $labelOptions = isset($htmlOptions['labelOptions']) ? $htmlOptions['labelOptions'] : array();
        unset($htmlOptions['labelOptions']);

        $items = array();
        $baseID = CHtml::getIdByName($name);
        $id = 0;
        foreach ($data as $value => $label) {
            $checked = !strcmp($value, $select);
            $htmlOptions['value'] = $value;
            $htmlOptions['id'] = $baseID . '_' . $id++;
            $option = CHtml::radioButton($name, $checked, $htmlOptions);
            $label = CHtml::label($label, $htmlOptions['id'], array_merge($labelOptions, HTML::$labelOptions));
            $items[] = strtr($template, array('{input}' => $option, '{label}' => $label));
        }

        $cnt = 0;
        $res = '<table width="100%">';
        $tr_opened = false;
        foreach ($items as $item) {
            if ($cnt % $columns == 0) {
                if ($tr_opened) $res .= "</tr>\n";
                $res .= "<tr>\n";
                $tr_opened = true;
            }
            $res .= "  <td>$item</td>\n";
            $cnt++;
        }
        if ($tr_opened) $res .= "</tr>\n";
        $res .= "</table>\n";
        return $res;
    }

    /**
     * Appends {@link errorCss} to the 'class' attribute.
     * @param array $htmlOptions HTML options to be modified
     */
    protected static function addErrorCss(&$htmlOptions)
    {
        if (isset($htmlOptions['class'])) {
            $htmlOptions['class'] .= ' ' . self::$errorCss;
        }
        else {
            $htmlOptions['class'] = self::$errorCss;
        }
    }

    /**
     * @var string the CSS class for highlighting error inputs. Form inputs will be appended
     * with this CSS class if they have input errors.
     */
    public static $errorCss = 'error';

    /**
     * Поле для ввода
     * @static
     * @param $model Модель
     * @param $attribute Атрибут
     * @return void
     */
    public static function TextField($model, $attribute)
    {
        echo "<p>";
        echo CHtml::activeLabel($model, $attribute, HTML::$labelOptions);
        echo CHtml::activeTextField($model, $attribute);
        echo "</p>";
    }

    /**
     * Вывод поля для загрузки файла
     * @static
     * @param $model модель
     * @param $attribute атрибут с файлом
     * @return void
     */
    public static function FileUpload($model, $attribute)
    {
        echo "<p>";
        echo CHtml::activeLabel($model, $attribute, HTML::$labelOptions);
        echo CHtml::activeTextField($model, $attribute);
        echo CHtml::activeFileField($model, $attribute);
        echo "</p>";
    }

    /**
     * Чекбокс
     * @param $model модель
     * @param $attribute атрибут
     */
    public static function CheckBox($model, $attribute)
    {
        echo "<p>";
        echo CHtml::activeCheckBox($model, $attribute);
        echo ' ';
        echo CHtml::activeLabel($model, $attribute, HTML::$labelOptions);
        echo "</p>";
    }

    public static $labelOptions = array('style' => 'display:inline');

}

