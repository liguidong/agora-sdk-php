<?php
/**
 * Author: liguidong94@gmail.com
 * Date: 2019/5/23 11:51 AM
 */

namespace Helper\Agora;

use Core\Core;
use General\Protocol\Http\Client;

abstract class Agora
{
    protected static $ins;
    protected static $appid;
    protected static $secret;
    protected static $API_DOMAIN = 'https://api.agora.io/dev/v1';
    protected $client;

    public function __construct()
    {
        static::$appid  = Core::config('agora', 'appid');//获取Agora配置 appid
        static::$secret = Core::config('agora', 'secret');//获取Agora配置 secret
        $this->client   = new Client(['userpwd'=>static::$appid.':'.static::$secret]);
    }

    private function __clone(){}

    /**
     * 单例
     */
    public static function getIns()
    {
        $class = get_called_class();
        if (!isset(static::$ins[$class])) {
            if (class_exists($class)) {
                static::$ins[$class] = new $class();
            }
        }

        return static::$ins[$class];
    }

    /**
     * 发送请求
     * @param string $method
     * @param string $uri
     * @param array  $params
     *
     * @return array
     */
    public function request(string $method, string $uri, array $params = [])
    {

        $url = static::$API_DOMAIN.$uri;

        switch ($method) {
            case 'GET':
                $res = $this->client->get($url,[static::$appid, static::$secret]);
                break;
            case 'POST':
                $res = $this->client->post($url,[static::$appid, static::$secret],[]);
                break;
            case 'DELETE':
                $res = $this->client->delete($url,$options);
                break;
            default:
                $res = $this->client->get($url,$options);
                break;
        }
        return json_decode($res, true);
    }
}