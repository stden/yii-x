<?php
/**
 * NextGame
 */
class NextGame
{
    const API_URL = "http://api2.nextgame.ru/api/";

    /**
     * @var int id приложения
     */
    public $app_id;

    /**
     * @var string метод
     */
    public $method;

    /**
     * @var int id пользователя
     */
    public $uid;

    /**
     * @var int secure = , 1 - Запрос Сервер-сервер
     */
    // public $secure;

    /**
     * @var string
     */
    public $clientKey;

    public $secret_key;

    public $usr_nickname;

    public $user_id;

    public $site_id;

    public $usr_first_name;

    public $t;

    // Сортировка параметров по имени и упаковка в строку
    public static function SortAndPack($params)
    {
        // сортируем параметры по ключу
        ksort($params);

        // объединяем пары ключ=значение в строку
        $paramsStr = '';
        foreach ($params as $key => $value) {
            $paramsStr .= $key . '=' . $value;
        }
        return $paramsStr;
    }

    /**
     * Генерация подписи параметров запроса по методу сервер—сервер
     * @param array $params параметры запроса
     * @param string $secretKey
     * @return string
     */
    public static function generateSignature(array $params, $secretKey)
    {
        $paramsStr = self::SortAndPack($params);

        // добавляем секретный ключ и вычисляем MD5-хэш
        $signature = md5($paramsStr . $secretKey);

        return $signature;
    }

    /**
     * Строка клиентского запроса
     * @return string
     */
    public function clientString()
    {
        $params = $this->clientParams();
        $paramsStr = self::SortAndPack($params);

        // sig = md5(uid + params + client_key)
        return $this->uid . $paramsStr . $this->clientKey;
    }

    public function clientParams()
    {
        // Берём все непустые поля
        $params = array_filter(get_object_vars($this));
        unset($params['clientKey']);

        //$params['secure'] = 0; // 0 - Запрос Клиент-сервер
        return $params;
    }

    public function serverParams()
    {
        // Берём все непустые поля
        $params = array_filter(get_object_vars($this));
        unset($params['secret_key']);

        $params['secure'] = 1; // 1 - Запрос Сервер-сервер
        return $params;
    }

    public function clientSig()
    {
        return md5($this->clientString());
    }

    public function clientQuery()
    {
        $params = $this->clientParams();
        $q = http_build_query($params);
        return self::API_URL . "?" . $q . "&sig=" . $this->clientSig();
    }

    public function serverString()
    {
        $params = $this->serverParams();
        $paramsStr = self::SortAndPack($params);

        // sig = md5(params + secret_key)
        return $paramsStr . $this->secret_key;
    }

    const CATALOG_URL = 'http://api2.nextgame.ru/iframe/js/catalogue/?';
    public $page_url;
    public $frame_id;
    public $tval;

    /**
     * Подпись запроса к каталогу
     * @param $params
     * @return string
     */
    public function catalogSig($params)
    {
        $paramsStr = NextGame::SortAndPack($params);
        // sig = md5(params + client_key)
        $s = $paramsStr . $this->clientKey;
        return md5($s);
    }

    /**
     * URL для загрузки из каталога
     * @param $params
     * @return string
     */
    public function catalogURL($params)
    {
        // <Подпись> - подпись запроса.
        $sig = $this->catalogSig($params);

        // Собираем URL для загрузки каталога
        $s = NextGame::CATALOG_URL;
        foreach ($params as $k => $v)
            $s .= "$k=$v&";
        $s .= 'sig=' . $sig;

        return $s;
    }

    /**
     * @return string Обработанный URL
     */
    public function EscapedPageURL()
    {
        return urlencode($this->page_url);
    }

    /**
     * Ссылка на игу по параметрам
     * @param $params
     * @return string
     */
    public function gameUrlByParams($params)
    {
        $s = "";
        foreach ($params as $k => $v)
            $s .= "$k=" . urlencode($v) . "&";

        return "http://api2.nextgame.ru/iframe/?{$s}sig=" . $this->catalogSig($params) .
            "&t=$this->t&page_url=" . $this->EscapedPageURL();
    }

    /**
     * @return string Подпись для игры
     */
    public function gameSig()
    {
        return $this->catalogSig($this->gameParams());
    }

    public function gameParams()
    {
        $p = [
            'site_id' => $this->site_id,
            'app_id' => $this->app_id,
            'frame_id' => $this->frame_id,
            'user_id' => $this->user_id,
            'tval' => $this->tval,
        ];
        if (!empty($this->usr_nickname)) {
            $p['custom_prm'] = 'usr_nickname=' . $this->usr_nickname;
        }
        return $p;
    }

    /**
     * @return string Ссылка для игр прямо по параметрам
     */
    public function gameURL()
    {
        return $this->gameUrlByParams($this->gameParams());
    }
}
