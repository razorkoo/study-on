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

    public function setTokens()
    {
        $this->adminToken = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE1NjIwMDQ4NzMsImV4cCI6MTU2MjAwODQ3Mywicm9sZXMiOlsiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoidXNlckBnbWFpbC5jb20ifQ.QFthw6CUJaX3lFfsaWo06ClhbbSEqCxut26A2lHlPhCR1_cC_e5z9-ICmNfXnK_FxOswWWdG1bFwuddDZqg4VI32G3JWDv_R_kk8e7pfpYym5SGbvncPr8o7xS3r5a-1nVfBGFe827nDc37-z9XAiNr6jmgejFzbwIhnscK1hqrvHGBTWbtLqqC4-0qZx34CuK8pTbiLiYic9pvsxLEZ5uYp6U4Q7CImzerobk5RMOaZtqg0anH30Nth0FUlBnks_z8kmzGcW8x2QupoBaK7K4N1YMI6wsKE5ZGQzC_c3apxB9DqnoiGcJsIM4V9Xa8O3IawBfr741DZ2m_Qu3rbFTEuGRsKWxfTkRazCQEg2bx3wZGk6E1mfWQnGx41duIjuQ1jQMk6rJBSTlDqZeKsZ1rAe4nmTNWaME9plUF3s_a42ItmwySMiixXGAnUHQNRiaRlevgsEOyrO0-lRj5uCA18lBWbw94_CMMuCj0WBJgP8baTl6aDgkbNsoAOF_DenFYK0mBbgSASqKXStXDkORqqAcz9ZVwYst7h_Yh-RCiXM2gcCC9zPj897jglttQkLkPq7vjRJMy_0ju-3Tjaw-yEZYIWp_rdmOC3LJbMHmFSELi0drsqZNTsfZKMGPq48D1EVCLfeyYcXg4KiBrUPoJA9hLxSWeS1UC4FAZPGI8";
        $this->userToken = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE1NjIwMDUwMTksImV4cCI6MTU2MjAwODYxOSwicm9sZXMiOlsiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoidXNlcjFAZ21haWwuY29tIn0.iYuoqWUhJYJbyv9q6cXe_bxMNM0xWR6wIa6j8kBSH9TTQBg_mMRqVTjfJYx_qVUsHqrWGXOf5EtlwKMtDLO0ViaH6ro78ZQCWXDHlEbn1TxxzDBipo53hnbZw8C43_MKwHi4QTA10xF-cZfATJoSi9x3DwLKIHckH2bJ5CnX7WnmSTEn9hZV_pe5aT0Rn4dIyq50YeYNX7QvtuJMS9b_iDtpl_o6c29JKfnJ8tyzRRPS2MCdjl8qXoCRoPLlwxm7xrB3podhoh1zbusu3uXqF7qP8xYAO10xowOaXs0yIQluRpOfkCU_gUQ0Jql3NWV3vKs8IujiwbF90oB_CNW4U9GgcnkSYcOMTuDgLVWP_pqwCoxNZ5VtvHl0Hz-a_5iK3KszbyTLqcZrPFWzPWe6J_UtuM7w3xXSWF2QKRzpMsrxY0SHWkioOTSOEl4XZjyYtvh2TaWoedn8HuRl4RiKY-vxF2mWqfVqFTpBYy31D5iQMGHNX9cg2TBFEEeG6PTxQp7utR0rPkMkRGGkB3NA6DmXzmOMsC9eDkFEZE_bw0Zzrfn4qMLYDOn0nEbzHvv7DXwdoArjccX-Lo4aAfkqH7YaIZAvrvxKuqePt_zetXn4TbCNiJSfLmR5m3II-UyGsqPdrPHf0ACrjA55nOVfP_PRBW38f4ptsK3aONIelmY";
    }
    public function login($username, $password)
    {
        $this->setTokens();
        $defaultUserName = "test@gmail.com";
        $defaultUserPassword = "aaaaaa";
        $adminUserName = "testadmin@gmail.com";
        $adminUserPassword = "aaaaaa";
        if ($username == $defaultUserName && $password == $defaultUserPassword) {
            return ['token' => $this->userToken, 'roles' => ["ROLE_USER"]];
        }
        if ($username == $adminUserName && $password == $adminUserPassword) {
            return ['token' => $this->adminToken, 'roles' => ["ROLE_USER", "ROLE_SUPER_ADMIN"]];
        } else {
            return ['code' => 401, 'message' => 'Bad credentials'];
        }
    }

    public function register($email, $passowrd)
    {
        $checkEmail= "testUser@gmail.com";

        if ($email == $checkEmail) {
            return ['code' => 400, 'errors' => "The Same user is already exist"];
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['code' => 400, 'errors' => ['Wrong email format']];
        }

        return ['userToken' => $this->userToken, 'roles' => ["ROLE_USER"]];

    }
}