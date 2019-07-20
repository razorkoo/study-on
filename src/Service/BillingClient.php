<?php

namespace App\Service;

use http\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Twig\Environment;

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
    public function register($email, $passowrd)
    {
        return $this->curlExec("POST", "/api/v1/register", json_encode(['email'=>$email, 'password'=>$passowrd]));
    }
    public function sendRefreshRequest($refreshToken)
    {
        return $this->curlExec("POST", '/api/v1/token/refresh', json_encode(['refresh_token' => $refreshToken]));
    }
    public function getCurrentInformation($token)
    {
        return $this->curlExec("GET", '/api/v1/users/current', "", $token);
    }

    public function getTransactions($token, array $filters = null)
    {
        $url = '/api/v1/transactions';
        if ($filters) {
            $url = $url . '?' . http_build_query($filters);
        }
        return $this->curlExec("GET", $url, "", $token);
    }

    public function getAllCourses()
    {
        return $this->curlExec("GET", "/api/v1/courses", "");
    }
    public function getDetailCourseInfo($course)
    {
        return $this->curlExec("GET", "/api/v1/courses/$course", "");
    }
    public function payCourse($course, $token)
    {
        return $this->curlExec("GET", "/api/v1/courses/$course/pay", "", $token);
    }
    public function addCourse($slug, $price, $type, $token)
    {
        return $this->curlExec("POST", "/api/v1/courses/add", json_encode(['code' => $slug, 'type'=>$type, 'price'=>$price]), $token);
    }
    public function editCourse($code, $slug, $price, $type, $token)
    {
        return $this->curlExec("POST", "/api/v1/courses/$code", json_encode(['code' => $slug, 'type'=>$type, 'price'=>$price]), $token);
    }
    public function deleteCourse($code, $token)
    {
        return $this->curlExec("DELETE", "/api/v1/courses/$code", "", $token);
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
        $parsedResults = json_decode($results, true);
        if ($parsedResults == null) {
            return "Invalid JSON";
        } else {
            return $parsedResults;
        }
    }
    public function curlExec($method, $url, $data, $token = null)
    {

        $curlExecutor = curl_init($this->host . $url);
        $returnedData = "";
        if (!$token) {
            curl_setopt($curlExecutor, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        } else {
            curl_setopt($curlExecutor, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Authorization: Bearer ' . $token]);
        }
        if ($method=="POST") {
            try {
                curl_setopt($curlExecutor, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curlExecutor, CURLOPT_POST, 1);
                curl_setopt($curlExecutor, CURLOPT_POSTFIELDS, $data);

                $returnedData = curl_exec($curlExecutor);
                curl_close($curlExecutor);
            } catch(\Exception $ex) {
                throw new HttpException(503, curl_error($curlExecutor));
            }
            if ($this->checkResults($returnedData)) {

                throw new HttpException(503, curl_error($curlExecutor));
            } else {
                $parsedData = $this->checkJson($returnedData);
                if ($parsedData == "Invalid JSON") {
                    throw new HttpException(503, "Invalid JSON");
                } else {
                    return $parsedData;
                }
            }
        }
        if ($method == "GET") {
            curl_setopt($curlExecutor, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curlExecutor, CURLOPT_HTTPGET, 1);
            $returnedData = curl_exec($curlExecutor);
            curl_close($curlExecutor);
            if ($this->checkResults($returnedData)) {
                throw new \HttpException(503, curl_error($curlExecutor));
            } else {
                $parsedData = $this->checkJson($returnedData);
                if ($parsedData == "Invalid JSON") {
                    throw new HttpException(503, "Invalid JSON");
                } else {
                    return $parsedData;
                }
            }
        }
        if ($method == "DELETE") {
            curl_setopt($curlExecutor, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curlExecutor, CURLOPT_CUSTOMREQUEST, $method);
            $returnedData = curl_exec($curlExecutor);
            curl_close($curlExecutor);
            if ($this->checkResults($returnedData)) {
                throw new \HttpException(503, curl_error($curlExecutor));
            } else {
                $parsedData = $this->checkJson($returnedData);
                if ($parsedData == "Invalid JSON") {
                    throw new HttpException(503, "Invalid JSON");
                } else {
                    return $parsedData;
                }
            }
        }
    }
    public function decodePayload($token)
    {
        $tokenParts = explode(".", $token);
        $tokenPayload = base64_decode($tokenParts[1]);
        $jwtPayload = json_decode($tokenPayload);
        return $jwtPayload;
    }
}