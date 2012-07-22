<?php
/**
 * Получение информации с http://www.ipinfodb.com
 */
class IPInfoDB
{
    /**
     * @var string Ключ для сервиса
     *
     */
    public $your_api_key;

    public function __construct($key)
    {
        $this->your_api_key = $key;
    }

    /**
     * @param $ip string IP-адрес для которого определить страну
     * @return mixed
     */
    public function getData($ip)
    {
        $url = "http://api.ipinfodb.com/v3/ip-city/?key={$this->your_api_key}&ip=$ip&format=json";
        $d = file_get_contents($url);
        return json_decode($d, true);
    }
}
