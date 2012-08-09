<?php
/**
 * Браузер пользователя
 */
class Browser
{
    const Internet_Explorer = 'Internet Explorer';
    const Google_Chrome = 'Google Chrome';
    const Firefox = 'Mozilla Firefox';
    const Safari = 'Safari';
    const Opera = 'Opera';

    /**
     * @param $browser Браузер
     * @param $version Версия
     */
    public function __construct($browser, $version)
    {
        $this->browser = $browser;
        $this->version = $version;
    }

    /**
     * @return string Строка для пользователя с названием браузера и версией
     */
    public function __toString()
    {
        return $this->browser . ' ' . $this->version;
    }

    /**
     * @param $user_agent
     * @return string
     */
    public static function user_browser($user_agent)
    {
        preg_match("/(MSIE|Opera|Firefox|Chrome|Version|Opera Mini|Netscape|Konqueror|SeaMonkey|Camino|Minefield|Iceweasel|K-Meleon|Maxthon)(?:\/| )([0-9.]+)/", $user_agent, $browser_info); // регулярное выражение, которое позволяет отпределить 90% браузеров
        list(, $browser, $version) = $browser_info; // получаем данные из массива в переменную
        if (preg_match("/Opera ([0-9.]+)/i", $user_agent, $opera))
            return 'Opera ' . $opera[1]; // определение _очень_старых_ версий Оперы (до 8.50), при желании можно убрать
        if ($browser == 'MSIE') { // если браузер определён как IE
            preg_match("/(Maxthon|Avant Browser|MyIE2)/i", $user_agent, $ie); // проверяем, не разработка ли это на основе IE
            if ($ie) return $ie[1] . ' based on IE ' . $version; // если да, то возвращаем сообщение об этом
            return new Browser(Browser::Internet_Explorer, $version); // иначе просто возвращаем IE и номер версии
        }
        if ($browser == 'Firefox') { // если браузер определён как Firefox
            preg_match("/(Flock|Navigator|Epiphany)\/([0-9.]+)/", $user_agent, $ff); // проверяем, не разработка ли это на основе Firefox
            if ($ff) return new Browser(Browser::Firefox, $ff[2]); // если да, то выводим номер и версию
        }
        if ($browser == 'Opera' && $version == '9.80')
            return new Browser(Browser::Opera, substr($user_agent, -5)); // если браузер определён как Opera 9.80, берём версию Оперы из конца строки
        if ($browser == 'Version')
            return new Browser(Browser::Safari, $version); // определяем Сафари
        if (!$browser && strpos($user_agent, 'Gecko')) return 'Browser based on Gecko'; // для неопознанных браузеров проверяем, если они на движке Gecko, и возращаем сообщение об этом
        if ($browser == 'Chrome')
            return new Browser(Browser::Google_Chrome, $version);

        return new Browser($browser, $version); // для всех остальных возвращаем браузер и версию
    }

    /**
     * Определения браузера пользователя по HTTP_USER_AGENT
     * В $_SERVER['HTTP_USER_AGENT'] PHP складывает User-Agent отправляемый браузером.
     * Пример User-Agent:
     *   Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/21.0.1180.60 Safari/537.1
     * @return Browser Браузер
     */
    public static function Detect()
    {
        return Browser::user_browser($_SERVER['HTTP_USER_AGENT']);
    }

    /**
     * Выводим все браузеры поддержвающие перетаскивания файлов
     */
    public static function Browsers()
    {
        // Браузер => Ссылка
        $br = [
            'Chrome' => 'http://www.google.ru/chrome',
            'Firefox' => 'http://www.mozilla.org/ru/firefox/new',
            'Opera' => 'http://www.opera.com',
            'Safari' => 'http://www.apple.com/ru/safari/download',
        ];
        $links = [];
        foreach ($br as $k => $v) {
            $links[] = '<a href="' . $v . '">' . $k . '</a>';
        }
        echo implode(' / ', $links);
    }

    /**
     * @static Вывод инструкции как выгружать закладки из браузера
     */
    public static function Bookmarks_ShowMeInstruction()
    {
        $browser = Browser::Detect();
        switch ($browser->browser) {
            case Browser::Google_Chrome:
                echo CHtml::link('1. ' . Yii::t('Browser.browser', 'Export Google Chrome bookmarks to file'),
                    'http://www.google.com/bookmarks/bookmarks.html');
                break;
            default:
                throw new Exception($browser->browser);
        }
    }

}
