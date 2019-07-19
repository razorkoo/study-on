<?php
/**
 * Created by IntelliJ IDEA.
 * User: alexander
 * Date: 2019-07-01
 * Time: 20:53
 */

namespace App\Tests\mock;


use App\Service\BillingClient;

class BillingClientMock extends BillingClient
{
    private $adminToken;
    private $userToken;
    private $refreshTokenAdmin;
    private $refreshTokenUser;
    private $allCourses = [['code'=>'kurs-programmirovaniya-na-c','type'=>'full','price'=>'15'],
        ['code'=>'kurs-veb-razrabotki-dlya-novichkov3','type'=>'rent','price'=>'10'],
        ['code'=>'kurs-veb-razrabotki-dlya-novichkov','type'=>'rent','price'=>'8'],
        ['code'=>'eto-testovyy-urok','type'=>'full','price'=>'20']];
    public function setTokens($newTokenAdmin = null, $newTokenUser = null)
    {

            $this->adminToken = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE1NjM1MjA3MDcsImV4cCI6MTU2MzYwNzEwNywicm9sZXMiOlsiUk9MRV9TVVBFUl9BRE1JTiIsIlJPTEVfVVNFUiJdLCJ1c2VybmFtZSI6InRlc3RhZG1pbjNAZ21haWwuY29tIn0.GGo3rrxUGQDcN8X0UhnD9IjcMu3AFsPUEsW6EflGm_itBjxbJ44GLhejmIvULRbWU5SnXGwJwVgHLHzFS2V78EztN1SFdqlBCHMcxQp6qtiqlHeEUTlhas76UtiIsDWW6s4yXCP487RZ6pTKzTyIexFlpXDCIDfi7KIXzZQbJx7dyeLgOrUSxMnaEj5X8us_G51zzjq2BpLT8VwgSNFjj4dLc2Rnn_nlInATk0zOw85jycrTczIlfBbILZVJoDejH6dj3nNf6lBuG_GyvdQpYWqWEmD0XOinsGnZxI4pHbvyhAMToRizcRAcN8C0BLWeWbTKXh8-GYiPdACNT4qD28R27ptnpCC5kzYS8Zz-icsu8nTASdzZNBjWPORvaDSdUq3vs1CLEdDLuO4xqZ7sCSyF26aYZ-M9FhLG3ZyrYBNKo1xWX_aQNiVEdyFWJeIW3bpn_hRyxwRO2YQ-Xp-TRe3h9L9BtcrrW52t4ZyVj-_0HhTUFA1MZjS3SH0HFiOjza0x1ERiuUXtLMMRUGJ0J9uoS24lxO_6SNI_NCy4NINrgnt0H0uVzR7l-qdGRQY8ibotWr0LlRvA9mZl2B24rQcPuvkoamN0X95deXvQb7KDaeWiCvmxxVUuPORr0MW04TnE3b2Diiza8GZvhDEtAi0uPHKL1TFoBJqPIFCDw0A";
            $this->userToken = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE1NjM1MjI5NDcsImV4cCI6MTU2MzYwOTM0Nywicm9sZXMiOlsiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoidGVzdHVzZXJAZ21haWwuY29tIn0.jmkFN3wskC80iQXRMaHN0VbLCsW4KKsitudGrHuFKn8LPUguPw71ofkB4ETrCYjXrOrqs582oWgClNmsjpvXqJvCHwRNBc5yhV83bXdN3EyHKlYUjnWcASGRAu841FkEpBdlvj614_CePRKuy5dyr3FjMtI28wfcRkZeV4WaidlnnSXnVKGDbtws34p-Gx99VG7hQvYlZUKbfv-LkDtvqq-ib-mSrFr621Uh-NgzJryZwdr485Su9sW4LXqjim6fJWQW0ke0MXVLuyA9Vj_AroZ82lR1-lsIKi-P0Ng7iXZnw-uSJxoM_Zi8G1NNHttOrmgiGXW-h9Dwdsvx3fZ8Ql2CxPIWGabyh-fXWDaVeWjy2MBTrXXMZ3RjI7L281_i4JVN5QyBR6WkEDNwf-C3xlIGkJaYF7NJcbKN06J2etPI_1SY85Ddy-6o1H1IzGDOlR6Y9tpLkWEyCO7Fw9fx8Nl4nqSlQGBpCm7XO_v2goWazsHIszWVfwGy0GhjlM23tOY9d7KfcN4lzw44VyBK2Rf04kLn7EF8IA8FXivMNaXbkptiGx6qd42YMTUOch_ffiJ_4zy0dxLZs-TR_79TjWh2pLOcZ7aFDfdQoQEIUjaKrDfeM0UiSejjdT-xyRcnAwm10hQHs0Sz9vu7XR8aQS3HeRKVKvDj2JWbNXAIUzo";
            $this->refreshTokenAdmin="f10287b97d4fdb7df6aea4a77aa25c7c3b6ec230b4e393823724e8d265f4f4e44fa2f65374c0529907be6c71521d29f22fe51053044779190fc22a3cc5a0b3d7";
            $this->refreshTokenUser="e7ba1e4646e71bc1bd82d9b94b2733e877565ac0b6a8992ce6d7db4763ef174e35665047d3aed9665ddc21814f37a4a9d4c40b144b4520f48b1969d3164a3b9c";
    }
    public function login($username, $password)
    {
        $this->setTokens();
        $defaultUserName = "test@gmail.com";
        $defaultUserPassword = "aaaaaa";
        $adminUserName = "testadmin@gmail.com";
        $adminUserPassword = "aaaaaa";
        if ($username == $defaultUserName && $password == $defaultUserPassword) {
            return ['token' => $this->userToken, 'roles' => ["ROLE_USER"], 'refresh_token'=>$this->refreshTokenUser];
        }
        if ($username == $adminUserName && $password == $adminUserPassword) {
            return ['token' => $this->adminToken, 'roles' => ['ROLE_USER', 'ROLE_SUPER_ADMIN'], 'refresh_token'=>$this->refreshTokenAdmin];
        } else {
            return ['code' => 401, 'message' => 'Bad credentials'];
        }
    }

    public function register($email, $passowrd)
    {
        $checkEmail= "testUser@gmail.com";
        $this->setTokens();
        if ($email == $checkEmail) {
            return ['code' => 400, 'errors' => "The Same user is already exist"];
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['code' => 400, 'errors' => ['Wrong email format']];
        }

        return ['userToken' => $this->userToken, 'roles' => ["ROLE_USER"], 'refresh_token'=>$this->refreshTokenUser];
    }
    public function sendRefreshRequest($refreshToken)
    {
            return ['token'=>$this->createToken("testadmin@gmail.com")];
    }
    public function getTransactions($token, array $filters = null)
    {
            if (isset($filters)) {
                if (array_key_exists('type', $filters)) {
                    if ($filters['type'] == 'deposite') {
                        $data = Array();
                        $ids = [1,2,3];
                        $amounts = [100,200,300];
                        for($i=0; $i < 3; $i++) {
                            $data[] = ['id' => $ids[$i], 'type' => 'deposite', 'amount'=>$amounts[$i]];
                        }
                        return $data;
                    }
                }
                if (array_key_exists('course_code', $filters)) {
                    if ($filters['course_code'] == 'kurs-programmirovaniya-na-c') {
                        return [['id' => 2, 'amount' => 10, 'type' => 'payment',
                            'course_code' => 'kurs-programmirovaniya-na-c',
                            'expired_at' => '2019-09-10 09:10:56']];
                    } else {
                        return ['message'=>'У вас нет транзакций'];
                    }
                }
            } else {
                if ($token == $this->adminToken) {
                    return [['id' => 1, 'amount' => 100, 'type' => 'deposit'],
                        ['id' => 2, 'amount' => 10, 'type' => 'payment',
                            'course_code' => 'kurs-programmirovaniya-na-c',
                            'expired_at' => '2019-09-10 09:10:56'],
                    ];
                } else {
                    return ['message'=>'У вас нет транзакций'];
                }
            }
    }
    public function getAllCourses()
    {

        return $this->allCourses;
    }
    public function getDetailCourseInfo($course)
    {
        foreach ($this->allCourses as $goal) {
            if ($goal['code'] == $course) {
                return $goal;
            }
        }
    }
    public function getCurrentInformation($token)
    {
        if ($token == $this->adminToken) {
            return ['username'=>'testadmin@gmail.com','roles'=>['ROLE_USER','ROLE_SUPER_ADMIN'],'balance'=>1000];
        }
        if ($token == $this->userToken) {
            return ['username'=>'test@gmail.com','roles'=>['ROLE_USER'],'balance'=>0];
        }
    }
    public function createToken($username)
    {
        $tokenHeader = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ.";
        $tokenSign = ".jk9pDZXhwKLmSwrjvPQM9ru-J3bpX2vow0KQEH0uAsgFCnbIvAsDgC_RiDgQXSgJ4K8iNZsu1IIF9CGGoOX80V6VMdKbD9Zx54puWLcu9eBQgARAeXgisEpoHKvjxm1FQIiUy7hKkCT-VN8LlsHLrDgdNvIha9Sc5MmyfTrp1I_3N2FD6s5mAaEgvC2uH1ZnOon0UkNm6QhWGpfwhQMmNvt6oV5MFf6uC1fkSPeUV-WyCw_myfr-waUr4jKnGf-KbQSAQsppUDUze676A8TyAUhflhk2nXS2P0KLNrvPdlzD2W905MAXxtrdmIoyxl7jwH5CCy59HbLiK30x5jLnsvgeXG0eifGVIUbfY-rTWLZ9fcoNy8YxsWT5EBmf1JWP8l1teYnC0IOiFJYzNhIdQUpGZ6OLCMl7mgvUOp_y6qJ5Qv4qWpFRM2X-9brm7ePA9fPhTBcjMDuJ81_pNuFZM07r3pBjae81vZRHWMpn41glUAOSzy4qi0yD13Rq55vkG-CepmgfIcQRmsIVRPd3P6v06I8luULqCUy6LGcz9LWKu7aeyqTbK0XFK7yQEhVEvst5CKyffERogL5TpHPLXRAfaK2BjgSUn_jgYeYtBVgF2M4qyMloK29R5odTD8cgmuvrPM_aAfrwhmRzS9ekybTjKxZx_WH2RMw0X3_cuiI";
        $payload = new \stdClass();
        $payload->iat = (new \DateTime())->getTimestamp();
        $payload->exp = ((new \DateTime())->modify('+3 week'))->getTimestamp();
        $payload->username = $username;
        if ($username == "testadmin@gmail.com") {
            $payload->roles = ['ROLE_USER', 'ROLE_SUPER_ADMIN'];
        }
        if ($username == "test@gmail.com") {
            $payload->roles = ['ROLE_USER'];
        }
        $base64Payload = base64_encode(json_encode($payload));
        $finalToken = $tokenHeader . $base64Payload . $tokenSign;
        return $finalToken;
    }
    public function payCourse($course, $token)
    {
        if ($course == 'kurs-programmirovaniya-na-c') {
            return ['success'=>true,'type'=>'full'];
        }
        if ($course == 'kurs-veb-razrabotki-dlya-novichkov3') {
            return ['success'=>true, 'type'=>'rent', 'expired_at'=>'2019-09-10 09:10:56'];
        }
        if ($course == 'kurs-veb-razrabotki-dlya-novichkov') {
            return ['success'=>true, 'type'=>'rent', 'expired_at'=>'2019-09-10 09:10:56'];
        }
    }
}