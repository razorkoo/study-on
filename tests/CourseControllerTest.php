<?php

namespace App\Tests;

use App\DataFixtures\CourseFixtures;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\mock\BillingClientMock;

class CourseControllerTest extends AbstractTest
{
    public function getFixtures(): array
    {
        return [CourseFixtures::class];
    }
    public function authClient($email, $password)
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
    public function testIndexPage()
    {
        $client = static::createClient();
        $client->disableReboot();
        $client->getContainer()->set('App\Service\BillingClient', new BillingClientMock($_ENV['BILLING_HOST']));
        $crawler = $client->request('GET', '/courses/');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }
    public function testNewPage()
    {
        $client = $this->authClient('testadmin@gmail.com', 'aaaaaa');
        $crawler = $client->request('GET', '/courses/');
        $this->assertSame(200,$client->getResponse()->getStatusCode());
        $client->clickLink('Добавить новый курс');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }
    public function testShowPage()
    {
        $client = static::createClient();
        $client->disableReboot();
        $client->getContainer()->set('App\Service\BillingClient', new BillingClientMock($_ENV['BILLING_HOST']));
        $crawler = $client->request('GET', '/courses/');
        $client->clickLink('Перейти к курсу');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }
    public function testShowCoursesPage()
    {
        $client = static::createClient();
        $client->disableReboot();
        $client->getContainer()->set('App\Service\BillingClient', new BillingClientMock($_ENV['BILLING_HOST']));
        $crawler = $client->request('GET', '/courses/');
        $client->clickLink('Перейти к курсу');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $client->clickLink('К списку курсов');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }
    public function testEditPage()
    {
        $client = $this->authClient('testadmin@gmail.com', 'aaaaaa');
        $crawler = $client->request('GET', '/courses/');
        $client->clickLink('Перейти к курсу');
        //print($client->getResponse()->getContent());
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $client->clickLink('Редактировать курс');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }
    public function testNewLessonPage()
    {
        $client = $this->authClient('testadmin@gmail.com', 'aaaaaa');
        $crawler = $client->request('GET', '/courses/');
        $client->clickLink('Перейти к курсу');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $client->clickLink('Добавить урок');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }
    public function testCountCourses()
    {
        $client = static::createClient();
        $client->disableReboot();
        $client->getContainer()->set('App\Service\BillingClient', new BillingClientMock($_ENV['BILLING_HOST']));
        $crawler = $client->request('GET', '/courses/');
        $this->assertEquals(3, $crawler->filter('.card')->count());
    }
    public function testPage404()
    {
        $courseId = "noname-kurs";
        $client = static::createClient();
        $client->disableReboot();
        $client->getContainer()->set('App\Service\BillingClient', new BillingClientMock($_ENV['BILLING_HOST']));
        $crawler = $client->request('GET', "/courses/$courseId");
        $this->assertSame(404, $client->getResponse()->getStatusCode());
    }
    public function testEditPage404()
    {
        $courseId = 404;
        $client = static::createClient();
        $client->disableReboot();
        $client->getContainer()->set('App\Service\BillingClient', new BillingClientMock($_ENV['BILLING_HOST']));
        $crawler = $client->request('GET', "/courses/$courseId/edit");
        $this->assertSame(404, $client->getResponse()->getStatusCode());
    }
    public function testCreateCoursePage()
    {
        $client = $this->authClient('testadmin@gmail.com', 'aaaaaa');
        $crawler = $client->request('GET', '/courses/');
        $before = $crawler->filter('.card')->count();
        $crawler = $client->clickLink('Добавить новый курс');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $newCourseForm = $crawler->selectButton('Сохранить')->form();
        $newCourseForm["course[title]"] = "Это тестовый урок2";
        $newCourseForm["course[description]"] = "Это описание тестового урока";
        $newCourseForm["course[price]"] = 0.0;
        $newCourseForm["course[type]"] = 'free';
        $client->submit($newCourseForm);
        $client->followRedirect();
        $crawler = $client->request('GET', '/courses/');
        $after = $crawler->filter('.card')->count();
        $this->assertGreaterThan($before, $after);
    }

    public function testEditCourse()
    {
        $client = $this->authClient('testadmin@gmail.com', 'aaaaaa');
        $client->request('GET', '/courses/');
        $client->clickLink('Перейти к курсу');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $crawler = $client->clickLink('Редактировать курс');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $courseForm = $crawler->selectButton('Сохранить')->form();
        $courseForm["course[title]"] = "Это тестовый урок измененный";
        $courseForm["course[description]"] = "это изменение описания урока";
        $courseForm["course[type]"] = "rent";
        $courseForm["course[description]"] = "12";
        $client->submit($courseForm);
        $client->followRedirect();
        $crawler = $client->request('GET', '/courses/');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('html:contains("Это тестовый урок измененный")')->count() > 0);
    }
    public function deleteCourse()
    {
        $client = $this->authClient('testadmin@gmail.com', 'aaaaaa');
        $client->request('GET', '/courses/');
        $crawler = $client->clickLink('Добавить новый курс');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $newCourseForm = $crawler->selectButton('Сохранить')->form();
        $newCourseForm["course[title]"] = "Это тестовый урок2";
        $newCourseForm["course[description]"] = "Это описание тестового урока2";
        $client->submit($newCourseForm);
        $client->request('GET', '/courses/');
        $crawler = $client->clickLink('Перейти к курсу');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $deleteCourseForm = $crawler->selectButton('Удалить')->form();
        $client->submit($deleteCourseForm);
        $crawler = $client->followRedirect();
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('html:contains("Это тестовый урок2")')->count() === 0);
    }
    public function newCourseWithoutTitle()
    {
        $client = $this->authClient('testadmin@gmail.com', 'aaaaaa');
         $client->request('GET', '/courses/');
        $crawler = $client->clickLink('Добавить новый курс');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $newCourseForm = $crawler->selectButton('Сохранить')->form();
        $newCourseForm["course[title]"] = "";
        $newCourseForm["course[description]"] = "Это описание тестового урока2";
        $client->submit($newCourseForm);
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Заполните это поле")')->count()
        );
    }
    public function newCourseWithoutDescription()
    {
        $client = $this->authClient('testadmin@gmail.com', 'aaaaaa');
        $client->request('GET', '/courses/');
        $crawler = $client->clickLink('Добавить новый курс');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $newCourseForm = $crawler->selectButton('Сохранить')->form();
        $newCourseForm["course[title]"] = "тест";
        $newCourseForm["course[description]"] = "";
        $client->submit($newCourseForm);
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Заполните это поле")')->count()
        );
    }
    public function noadminCreateCourseButton()
    {
        $client = $this->authClient('test@gmail.com', 'aaaaaa');
        $crawler = $client->request('GET', '/courses/');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertSame(0, $crawler->filter('Добавить новый курс')->count());
    }
    public function testEditCourseNoAdmin()
    {
        $client = $this->authClient('test@gmail.com', 'aaaaaa');
        $client->request('GET', '/courses/');
        $crawler = $client->clickLink('Перейти к курсу');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertSame(0, $crawler->filter('Редактировать курс')->count());
    }
    public function testDeleteCourseNoAdmin()
    {
        $client = $this->authClient('test@gmail.com', 'aaaaaa');
        $client->request('GET', '/courses/');
        //print($client->getResponse()->getContent());
        $crawler = $client->clickLink('Перейти к курсу');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertSame(0, $crawler->filter('Удалить')->count());
    }
    public function testBuyCourse()
    {
        $client = $this->authClient('testadmin@gmail.com', 'aaaaaa');
        $crawler = $client->request('GET', '/courses/kurs-veb-razrabotki-dlya-novichkov3');
        $client->clickLink('Арендовать');
        $client->clickLink('Подтвердить');
        $client->followRedirect();
        $this->assertContains('Курс успешно куплен', $client->getResponse()->getContent());
    }
    public function testBuyCourseNoMoney()
    {
        $client = $this->authClient('test@gmail.com', 'aaaaaa');
        $crawler = $client->request('GET', '/courses/kurs-veb-razrabotki-dlya-novichkov3');
        $this->assertContains('disabled', $client->getResponse()->getContent());
    }
}
