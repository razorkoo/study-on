<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\DataFixtures\CourseFixtures;
use App\Tests\mock\BillingClientMock;

class SecurityControllerTest extends AbstractTest
{
    public function getFixtures(): array
    {
        return [CourseFixtures::class];
    }
    public function login($email, $password)
    {
        $client = static::createClient();
        $client->disableReboot();
        $client->getContainer()->set('App\Service\BillingClient', new BillingClientMock($_ENV['BILLING_HOST']));
        $client->request('GET', '/courses/');
        $crawler = $client->clickLink("Вход");
        $form = $crawler->selectButton('Войти')->form();
        $form["email"] = $email;
        $form["password"] = $password;
        $client->submit($form);
        $client->followRedirect();
        return $client;
    }
    public function testLogin()
    {
        $client = $this->login('test@gmail.com', 'aaaaaa');

        $crawler = $client->request('GET', '/courses/');
        $this->assertTrue($crawler->filter('html:contains("Выйти")')->count() > 0);
    }
    public function testLoginWrong()
    {

        $client = static::createClient();
        $client->disableReboot();
        $client->getContainer()->set('App\Service\BillingClient', new BillingClientMock($_ENV['BILLING_HOST']));
        $client->request('GET', '/courses/');
        $crawler = $client->clickLink("Вход");
        $form = $crawler->selectButton('Войти')->form();
        $form["email"] = "testadmin@gmail.com";
        $form["password"] = "aaaaaaaa";
        $client->submit($form);
        $client->followRedirect();
        $this->assertContains("Bad credentials",$client->getResponse()->getContent());
    }
    public function testUserProfileAndLogout()
    {
        $client = $this->login('test@gmail.com', 'aaaaaa');
        $client->request('GET', '/courses/');
        $crawler = $client->clickLink('Профиль');
        $this->assertTrue($crawler->filter('html:contains("Роль пользователя: Пользователь")')->count() > 0);
        $client->request('GET', '/courses/');
        $client->clickLink('Выйти');
        $crawler = $client->followRedirect();
        $this->assertTrue($crawler->filter('html:contains("Вход")')->count() > 0);
    }
    public function testRegister()
    {
        $client = static::createClient();
        $client->disableReboot();
        $client->getContainer()->set('App\Service\BillingClient', new BillingClientMock($_ENV['BILLING_HOST']));
        $client->request('GET', '/courses/');
        $crawler = $client->clickLink("Регистрация");
        $form = $crawler->selectButton('Зарегистрироваться')->form();

        $form['registration_form[email]'] = "testuser@gmail.com";
        $form['registration_form[password]'] = "aaaaaa";
        $form['registration_form[confirmation]'] = "aaaaaa";
        $client->submit($form);
        $client->followRedirect();
        $this->assertContains("Выйти",$client->getResponse()->getContent());
    }
    public function testRegisterUserExists()
    {
        $client = static::createClient();
        $client->disableReboot();
        $client->getContainer()->set('App\Service\BillingClient', new BillingClientMock($_ENV['BILLING_HOST']));
        $client->request('GET', '/courses/');
        $crawler = $client->clickLink("Регистрация");
        $form = $crawler->selectButton('Зарегистрироваться')->form();

        $form['registration_form[email]'] = "testUser@gmail.com";
        $form['registration_form[password]'] = "aaaaaa";
        $form['registration_form[confirmation]'] = "aaaaaa";
        $client->submit($form);
        $this->assertContains("The Same user is already exist",$client->getResponse()->getContent());
    }
    public function testRegisterWrongEmail()
    {
        $client = static::createClient();
        $client->disableReboot();
        $client->getContainer()->set('App\Service\BillingClient', new BillingClientMock($_ENV['BILLING_HOST']));
        $client->request('GET', '/courses/');
        $crawler = $client->clickLink("Регистрация");
        $form = $crawler->selectButton('Зарегистрироваться')->form();

        $form['registration_form[email]'] = "testusergmail.com";
        $form['registration_form[password]'] = "aaaaaa";
        $form['registration_form[confirmation]'] = "aaaaaa";
        $client->submit($form);
        $this->assertContains("Wrong email format",$client->getResponse()->getContent());
    }

}
