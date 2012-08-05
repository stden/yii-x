<?php

require_once 'lib/simple_html_dom.php';

/**
 * Закладка в Google Chrome
 */
class ChromeBookmark
{
    /**
     * @var string URL сайта
     */
    public $url;

    /**
     * @var XDateTime время создания
     */
    public $created;

    /**
     * @var string Название закладки
     */
    public $name;

    /**
     * Импорт закладок из файла выгруженого из браузера Google Chrome
     * @static
     * @param $filename string Имя файла для импорта
     * @return array|\ChromeBookmark[]
     */
    public static function import($filename)
    {
        // Загружаем файл с закладками
        $html = file_get_html($filename);
        $bookmarks = [];
        /** @var $bookmarks ChromeBookmark[] */
        foreach ($html->find('a') as $element) { // Перебираем все ссылки в файле
            $b = new ChromeBookmark();
            $b->url = $element->href;
            $b->created = new XDateTime(date("Y-m-d H:i:s", $element->add_date));
            $b->name = $element->innertext;
            $bookmarks[] = $b;
        }
        return $bookmarks;
    }
}
