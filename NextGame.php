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
//    public $secure;

    public $clientKey;


    public $secret_key;

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

        $params['secure'] = 0; // 0 - Запрос Клиент-сервер
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

}
