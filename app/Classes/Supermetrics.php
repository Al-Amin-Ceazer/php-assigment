<?php

namespace App\Classes;

use App\Config;

class Supermetrics
{
    private $client_id;
    private $email;
    private $name;

    /**
     * Supermetrics constructor.
     */
    public function __construct()
    {
        Session::init();
        $this->client_id = Config::$client_id;
        $this->email     = Config::$email;
        $this->name      = Config::$name;
    }

    public function register()
    {
        $postData = [
            'client_id' => $this->client_id,
            'email'     => $this->email,
            'name'      => $this->name,
        ];

        $client = new CurlWrapper();
        $client->SendRequest('POST', 'https://api.supermetrics.com/assignment/register', $postData);
        $statusCode = $client->getStatusCode();

        $res = json_decode($client->getResponse(), true);

        if ($statusCode != 200) {
            echo $res['error']['message'];
            exit();
        }

        Session::set("sl_token_time", date('Y-m-d h:s:i'));
        Session::set("sl_token", $res['data']['sl_token']);
    }

    public function fetchPosts()
    {
        $sl_token = $this->checkSessionToken();

        if ($sl_token == false) {
            $this->register();
        }

        $dataArr      = [];
        $client       = new CurlWrapper();
        $numberOfPage = Config::$numberOfPage;

        for ($i = 1; $i <= $numberOfPage; $i++) {
            $parms = [
                'sl_token' => Session::get('sl_token'),
                'page'     => $i,
            ];

            $client->SendRequest('GET', 'https://api.supermetrics.com/assignment/posts', $parms);
            $response = json_decode($client->getResponse(), true);

            if ($client->getStatusCode() != 200) {
                die($response['error']['message']);
            }

            $dataArr[$response['data']['page']] = $response['data']['posts'];
        }

        return $this->arrayCombine($dataArr);
    }

    public function arrayCombine($array)
    {
        if (!is_array($array)) {
            return false;
        }
        $result = [];
        foreach ($array as $arr) {
            foreach ($arr as $val) {
                $result[] = $val;
            }
        }

        return $result;
    }

    public function checkSessionToken()
    {
        if (!empty(Session::get("sl_token_time"))) {
            return $this->checkTokenExpire(Session::get("sl_token_time"));
        }

        return false;
    }

    public function checkTokenExpire($time)
    {
        $current_time = strtotime(date('Y-m-d h:s:i'));
        $from_time    = strtotime($time);
        $min          = round(abs($current_time - $from_time) / 60, 2);

        if ($min > 60) {
            return false;
        }

        return true;
    }
}