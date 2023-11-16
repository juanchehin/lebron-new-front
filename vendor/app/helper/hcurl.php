<?php

class HCurl
{
    protected $header_data;
    protected $url;
    protected $json_data;
    protected $get_data;
    protected $data;
    protected $is_develop;
    protected $auth;

    public function __construct($url, $is_develop = false)
    {
        $this->setUrl($url);
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

        return $this;
    }

    public function setPath($value)
    {
        if ( !preg_match("#^\/#", $value) )
        {
            $value = "/" . $value;
        }
        $this->url .= $value;
    }

    private function setUrl($value)
    {
        $this->url = $value;

        return $this;
    }

    public function setAuth($username, $password)
    {
        $this->auth = "{$username}:{$password}";

        return $this;
    }

    public function setData($key, $value = null, $json = true)
    {
        $data = array();
        if ( is_array($key) )
        {
            foreach ($key as $k => $v)
            {
                $data[$k] = $v;
            }
        }
        else
        {
            $data[$key] = $value;
        }

        $this->get_data = http_build_query($data);
        $this->json_data = json_encode($data);
        $this->data = $json ? $this->json_data : $this->get_data;

        return $this;
    }

    public function callAPI($method = null)
    {
        $curlOpts = array(
            CURLOPT_HEADER         => 1,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => $this->header_data,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HEADER         => false
        );

        if ( $this->auth )
        {
            $curlOpts[CURLOPT_USERPWD] = $this->auth;
        }

        if ( $this->is_develop )
        {
            $curlOpts[CURLOPT_SSL_VERIFYHOST] = false;
            $curlOpts[CURLOPT_SSL_VERIFYPEER] = false;
        }

        switch ( strtolower($method) )
        {
            case "post":
                $curlOpts[CURLOPT_POST] = true;
                $http_verb = "POST";
                break;
            case "put":
                $curlOpts[CURLOPT_PUT] = 1;
                $http_verb = "PUT";
                break;
            case "delete" :
                $http_verb = "DELETE";
                break;
            default:
                $http_verb = "GET";
                if ( $this->get_data )
                {
                    $this->url .= "?" . $this->get_data;
                }
                break;
        }
        $curlOpts[CURLOPT_URL] = strtolower($this->url);
        $curlOpts[CURLOPT_CUSTOMREQUEST] = $http_verb;
        $curlOpts[CURLOPT_POSTFIELDS] = $this->data;
        $curl = curl_init();
        curl_setopt_array($curl, $curlOpts);
        $result = curl_exec($curl);
        if ( !$this->is_develop )
        {
            //HArray::varDump($curlOpts, false);
        }
        if ( false === $result )
        {
            $result['error'] = curl_errno($curl) . ": " . curl_error($curl);
        }
        curl_close($curl);

        return $result;
    }
}