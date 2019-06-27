<?php

namespace App\Service;

use http\Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BillingClient
{
    private $host;
    public function __construct($queriesHost)
    {
        $this->host = $queriesHost;
    }

    public function login($username, $password)
    {
        return $this->curlExec("POST", "/api/v1/login", json_encode(['username'=>$username,'password'=>$password]));
    }
    public function regiser($email,$passowrd)
    {
        return $this->curlExec("POST", "/api/v1/register", json_encode(['email'=>$email, 'password'=>$passowrd]));
    }


    public function checkResults($results)
    {
        if ($results == null) {
            return true;
        } else {
            return false;
        }
    }
    public function checkJson($results)
    {
        $parsedResults = json_decode($results,true);
        if ($parsedResults == null) {
            return "Invalid JSON";
        } else {
            return $parsedResults;
        }
    }
    public function curlExec($method, $url, $data)
    {
        $curlExecutor = curl_init($this->host . $url);
        $returnedData = "";
        if ($method=="POST") {
            curl_setopt($curlExecutor, CURLOPT_RETURNTRANSFER,1);
            curl_setopt($curlExecutor, CURLOPT_POST, 1);
            curl_setopt($curlExecutor, CURLOPT_POSTFIELDS, $data);
            curl_setopt($curlExecutor, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            $returnedData = curl_exec($curlExecutor);
            curl_close($curlExecutor);
            if ($this->checkResults($returnedData)) {
                throw new \HttpException(503, curl_error($curlExecutor));
            } else {
                $parsedData = $this->checkJson($returnedData);
                if ($parsedData == "Invalid JSON") {
                    throw new \HttpException(503,"Invalid JSON");
                } else {
                    return $parsedData;
                }
            }
        }
        if ($method == "GET") {
            curl_setopt($curlExecutor, CURLOPT_RETURNTRANSFER,1);
            curl_setopt($curlExecutor, CURLOPT_HTTPGET, 1);
            curl_setopt($curlExecutor, CURLOPT_POSTFIELDS, $data);
            curl_setopt($curlExecutor, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            $returnedData = curl_exec($curlExecutor);
            curl_close($curlExecutor);
            if ($this->checkResults($returnedData)) {
                throw new \HttpException(503, curl_error($curlExecutor));
            } else {
                $parsedData = $this->checkJson($returnedData);
                if ($parsedData == "Invalid JSON") {
                    throw new \HttpException(503,"Invalid JSON");
                } else {
                    return $parsedData;
                }
            }
        }
    }
}