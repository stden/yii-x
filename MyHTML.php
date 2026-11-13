<?php
/**
 * Функции вёрстки, удобные в этом проекте
 */
class MyHTML
{
    /**
     * Вставка картинки, только если она существует
     * @static
     * @param $path
     */
    public static function image($path, $id = null)
    {
        $pathOnServer = self::webRoot() . '/' . $path;
        if (is_null($id))
            $id = uniqid();

        if (file_exists($pathOnServer)) {
            echo CHtml::image(self::baseUrl() . '/' . $path, $path,
                ["class" => "clickme", "title" => 'Фото',
                    'id' => $id
                    // "width"=>"200", "height"=>"300"
                ]
            );
        } else {
            echo "Файла $path на сервере нет!";
        }
    }

    /**
     * Базовый URL сайта
     * @static
     * @return string
     */
    public static function baseUrl()
    {
        return Yii::app()->request->baseUrl;
    }

    /**
     * Каталог на сервере с корнем сайта
     * @static
     * @return mixed
     */
    public static function webRoot()
    {
        return Yii::getPathOfAlias('webroot');
    }
}
