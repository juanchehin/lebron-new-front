<?php

class CurlClass
{
    protected $header_data;
    protected $url = null;
    protected $data;
    protected $is_develop;
    protected $auth;
    protected $end_point = null;
    protected static $_data;

    public function __construct($url, $is_develop = false)
    {
        $this->url = $url;
        #-- Está en modo desarrollo?
        $this->is_develop = $is_develop;
        //$this->setHeaderData("X-UTC", time());
    }

    public function setHeaderData($key, $value = null)
    {
        if ( is_array($key) )
        {
            foreach ((array)$key as $k => $v)
            {
                $this->header_data[] = "{$k}:{$v}";
            }
        }
        else
        {
            $this->header_data[] = "{$key}:{$value}";
        }

        return;
    }

    public function setPath($value)
    {
        if ( !preg_match("#^\/#", $value) )
        {
            $value = "/" . $value;
        }
        //$this->url .= $value;
        $this->end_point = $value;
    }

    public function setAuth($username, $password)
    {
        $this->auth = "{$username}:{$password}";

        return;
    }

    public function setData($key, $value = null, $json = true)
    {
        #--
        if ( is_array($key) )
        {
            foreach ($key as $k => $v)
            {
                static::$_data[$k] = $v;
            }
        }
        else
        {
            static::$_data[$key] = $value;
        }
        $this->data = $json ? json_encode(static::$_data) : http_build_query(static::$_data);
        return;
    }

    public function callAPI($method = null)
    {
        #-- here
        $curlOpts[CURLOPT_RETURNTRANSFER] = true;
        $curlOpts[CURLOPT_HEADER] = false;
        $curlOpts[CURLOPT_HTTPHEADER] = $this->header_data;
        $curlOpts[CURLOPT_FOLLOWLOCATION] = 1;
        //$curlOpts[CURLOPT_TIMEOUT] = 20;
        //$curlOpts[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_1;
        if ( $this->auth )
        {
            $curlOpts[CURLOPT_USERPWD] = $this->auth;
        }
        $this->url .= $this->end_point;
        switch ( strtolower($method) )
        {
            case "post":
                $curlOpts[CURLOPT_POST] = true;
                $http_verb = "POST";
                break;
            case "put":
                $curlOpts[CURLOPT_PUT] = true;
                $http_verb = "PUT";
                break;
            case "delete" :
                $http_verb = "DELETE";
                break;
            default:
                $http_verb = "GET";
                if ( $this->data )
                {
                    $this->url .= "?" . $this->data;
                }
                break;
        }
        $curlOpts[CURLOPT_URL] = $this->url;
        $curlOpts[CURLOPT_CUSTOMREQUEST] = $http_verb;
        $curlOpts[CURLOPT_POSTFIELDS] = $this->data;
        if ( $this->is_develop || true )
        {
            $curlOpts[CURLOPT_SSL_VERIFYHOST] = false;
            $curlOpts[CURLOPT_SSL_VERIFYPEER] = false;
        }
        #--
        //HArray::varDump($curlOpts);
        $curl = curl_init($this->url);
        //HArray::varDump($this->url, false);
        //curl_setopt_array($curl, $curlOpts);
        foreach ($curlOpts as $key => $value)
        {
            curl_setopt($curl, $key, $value);
        }
        $result = curl_exec($curl);
        //HArray::varDump($result);
        if ( !$this->is_develop )
        {
            //HArray::varDump($curlOpts, false);
        }
        if ( $err = curl_error($curl) )
        {
            //$result['error'] = curl_errno($curl) . ": " . curl_error($curl);
            $result['error'] = "Error: {$err}";
        }
        curl_close($curl);
        //$this->data = null;
        $this->url = str_ireplace($this->end_point, "", $this->url);
        return json_decode($result, true);
    }
}