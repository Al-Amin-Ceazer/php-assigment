<?php

namespace App\Classes;

class CurlWrapper
{
    /**
     * @var
     */
    private $statusCode;
    private $body;

    /**
     * CurlWrapper constructor.
     */
    public function __construct()
    {
        $this->clear();
    }

    /**
     * Defining all the default data for all the variable
     */
    public function clear()
    {
        $this->statusCode = null;
        $this->body       = null;
    }

    /**
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->body;
    }

    /**
     * @param      $method
     * @param      $url
     * @param      $data
     * @param bool $headers
     *
     */
    public function SendRequest($method, $url, $data, $headers = false)
    {
        $curl = curl_init();
        switch ($method) {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);
                if ($data) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                }
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                if ($data) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                }
                break;
            default:
                if ($data) {
                    $url = sprintf("%s?%s", $url, http_build_query($data));
                }
        }
        // OPTIONS:
        curl_setopt($curl, CURLOPT_URL, $url);
        if (!$headers) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
            ]);
        } else {
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                $headers,
            ]);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        // EXECUTE:
        $result = curl_exec($curl);
        if (!$result) {
            $result = $this->curlShowError($curl);
        }
        $this->statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $this->body = $result;
    }

    /**
     * Get the curl error
     *
     * @param $curl
     *
     * @return string
     */
    private function curlShowError($curl)
    {
        return 'Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl);
    }
}