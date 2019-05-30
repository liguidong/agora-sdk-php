<?php
/**
 *
 * @author
 * @copyright Copyright (c) Beijing CmsTop Technology Co.,Ltd.
 */

namespace General\Protocol\Http;

class Client
{
    /**
     * @var array 默认设置
     */
    protected static $defaultOptions = array(
        'userAgent' => 'Top Robot 1.0',     // 请求时的 user agent
        'connectionTimeout' => 10,          // 发起连接前等待超时时间
        'timeout' => 30,                    // 请求执行超时时间
        'sslVerifypeer' => false            // 是否从服务端进行验证
    );

    protected $options;

    /**
     * @var int HTTP 状态码
     */
    protected $httpCode;

    /**
     * @var array HTTP head
     */
    protected $httpHead;

    /**
     * @var array 响应信息
     */
    protected $httpInfo = array();

    /**
     * @var string 错误
     */
    protected $error;

    /**
     * @var array 通用的header
     */
    protected $requestHeaders = [];

    /**
     * @var string 错误代码
     */
    protected $errno;

    public function __construct(array $options = array())
    {
        $this->options = array_merge(self::$defaultOptions, $options);
    }

    /**
     * @return mixed
     */
    public function getHttpCode()
    {
        return $this->httpCode;
    }

    /**
     * @return mixed
     */
    public function getErrno()
    {
        return $this->errno;
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return mixed
     */
    public function getHttpInfo()
    {
        return $this->httpInfo;
    }

    /**
     * @return string
     */
    public function getHttpHead()
    {
        return $this->httpHead;
    }

    public function get($url, $params = array(), array $extra_headers = array())
    {
        return $this->request($url, $params, 'GET', array(), $extra_headers);
    }

    public function post($url, $params = array(), array $multipart = array(), array $extra_headers = array(),$is_json = false)
    {
        return $this->request($url, $params, 'POST', $multipart, $extra_headers, $is_json);
    }
    public function delete($url, $params = array(), array $multipart = array(), array $extra_headers = array())
    {
        return $this->request($url, $params, 'DELETE', $multipart, $extra_headers);
    }

    protected function request($url, $params = array(), $method = 'GET', array $multipart = array(), array $extra_headers = array(),$is_json = false)
    {
        $method = strtoupper($method);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_USERAGENT, $this->options['userAgent']);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $this->options['connectionTimeout']);
        curl_setopt($curl, CURLOPT_TIMEOUT, $this->options['timeout']);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, $this->options['sslVerifypeer']);
        curl_setopt($curl, CURLOPT_HEADER, true);
        if (!empty($this->options['userpwd'])) {
            curl_setopt($curl, CURLOPT_USERPWD, $this->options['userpwd']);
        }

        $headers = array_merge($this->requestHeaders, (array)$extra_headers);

        /**
         * 解决响应结果中包含额外的 100 continue 的问题
         *
         * @see http://www.iandennismiller.com/posts/curl-http1-1-100-continue-and-multipartform-data-post.html
         * @see http://stackoverflow.com/questions/11359276/php-curl-exec-returns-both-http-1-1-100-continue-and-http-1-1-200-ok-separated-b
         */
        $headers[] = 'Expect: ';

        switch ($method) {
            case 'POST':
                curl_setopt($curl, CURLOPT_POST, true);
                if (!empty($params)) {
                    if ($multipart) {
                        foreach ($multipart as $key => $file) {
                            $params[$key] = '@' . $file;
                        }
                        curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
                    } else {
                        $params = is_array($params) ? ($is_json ? json_encode($params): http_build_query($params)) : $params;
                        curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
                    }
                }
                break;
            case 'DELETE':
            case 'GET':
                if ($method == 'DELETE') {
                    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
                }
                if (!empty($params)) {
                    $url = $url . (strpos($url, '?') !== false ? '&' : '?')
                        . (is_array($params) ? http_build_query($params) : $params);
                }
                break;
        }

        curl_setopt($curl, CURLINFO_HEADER_OUT, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_URL, $url);
        $response = curl_exec($curl);
        $this->httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $httpInfo = curl_getinfo($curl);
        if (is_array($httpInfo) && !empty($httpInfo)) {
            $this->httpInfo = array_merge($this->httpInfo, $httpInfo);
        }
        curl_close($curl);
        $this->httpHead = array();
        $split = preg_split("#(\r\n|\n\r){2}#", $response, 2);

        if (count($split) === 2) {
            list($httpHead, $response) = $split;
            if (!$response) {
                $response = $httpHead;
            } else {
                foreach ((array)explode("\n", $httpHead) as $line) {
                    $split = explode(': ', $line);
                    if (count($split) !== 2) {
                        continue;
                    }
                    $this->httpHead[] = $line;
                }
            }
        }
        return $response;
    }

    function setRequestHeaders($headers){
        $this->requestHeaders = (array) $headers;
    }

    function getRequestHeaders(){
        return $this->requestHeaders;
    }
}