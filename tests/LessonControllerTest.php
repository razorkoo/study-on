<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\DataFixtures\CourseFixtures;
use App\Tests\mock\BillingClientMock;

class LessonControllerTest extends AbstractTest
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
    public function testLessonsPage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/lessons/');
        $this->assertSame(404, $client->getResponse()->getStatusCode());
    }
    public function testShowLesson()
    {
        $client = $this->authClient('test@gmail.com', 'aaaaaa');
        $client->request('GET', '/courses/');
        $crawler = $client->clickLink('Перейти к курсу');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $link = $crawler->filter('.btn-link')->first();
        $crawler = $client->click($link->link());
        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }
    public function testAddNewLesson()
    {
        $client = $this->authClient('testadmin@gmail.com', 'aaaaaa');
        $client->request('GET', '/courses/');
        $crawlerCourse = $client->clickLink('Перейти к курсу');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $countOfLessonsBefore = $crawlerCourse->filter('.btn-link')->count();
        $crawler = $client->clickLink('Добавить урок');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $newLessonForm = $crawler->selectButton('Сохранить')->form();
        $newLessonForm["lesson[title]"] = "Это тестовый урок";
        $newLessonForm["lesson[content]"] = "Это описание тестового урока";
        $newLessonForm["lesson[serialNumber]"] = "2";
        $client->submit($newLessonForm);
        $client->request('GET', '/courses/');
        $crawler = $client->clickLink('Перейти к курсу');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan($countOfLessonsBefore, $crawler->filter('.btn-link')->count());
    }
    public function testInvalidLesson()
    {
        $client = $this->authClient('testadmin@gmail.com', 'aaaaaa');
        $client->request('GET', '/courses/');
        $crawlerCourse = $client->clickLink('Перейти к курсу');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $countOfLessonsBefore = $crawlerCourse->filter('.btn-link')->count();
        $crawler = $client->clickLink('Добавить урок');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $newLessonForm = $crawler->selectButton('Сохранить')->form();
        $newLessonForm["lesson[title]"] = "тестовое название";
        $newLessonForm["lesson[content]"] = "Это описание тестового урока";
        $newLessonForm["lesson[serialNumber]"] = "";
        $client->submit($newLessonForm);
        $this->assertContains("This value should not be blank.", $client->getResponse()->getContent());
    }
    public function testPage404()
    {
        $courseId = 404;
        $client = static::createClient();
        $crawler = $client->request('GET', "/lessons/$courseId");
        $this->assertSame(404, $client->getResponse()->getStatusCode());
    }
    public function testEditPage404()
    {
        $courseId = 404;
        $client = static::createClient();
        $crawler = $client->request('GET', "/lessons/$courseId/edit");
        $this->assertSame(404, $client->getResponse()->getStatusCode());
    }

    public function testEditLesson()
    {
        $client = $this->authClient('testadmin@gmail.com', 'aaaaaa');
        $client->request('GET', '/courses/');
        $crawler = $client->clickLink('Перейти к курсу');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $link = $crawler->filter('.btn-link')->eq(1);
        $client->clickLink($link->text());
        $crawler = $client->clickLink('Редактировать');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $form = $crawler->selectButton('Сохранить')->form();
        $form["lesson[title]"] = "Новый урок измененый";
        $form["lesson[content]"] = "Новое описание урока";
        $form["lesson[serialNumber]"] = 7;
        $client->submit($form);
        $client->request('GET', '/courses/');
        $crawler = $client->clickLink('Перейти к курсу');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('html:contains("Новый урок измененый")')->count() > 0);
    }
    public function deleteLesson()
    {
        $client = $this->authClient('testadmin@gmail.com', 'aaaaaa');
        $client->request('GET', '/courses/');
        $crawler = $client->clickLink('Перейти к курсу');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $links = $crawler->filter('lesson')->links();
        $countOfLessonsBefore = count($links);
        $crawler = $client->click($links[0]);
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $deleteLessonForm = $crawler->selectButton('Удалить')->form();
        $client->submit($deleteLessonForm);
        $crawler = $client->request('GET', '/courses/');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertCount(($countOfLessonsBefore-1), $crawler->filter('.btn-link')->count());
    }
    public function testAddNewLessonNoAdmin()
    {
        $client = $this->authClient('test@gmail.com', 'aaaaaa');
        $client->request('GET', '/courses/');
        $crawlerCourse = $client->clickLink('Перейти к курсу');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $countOfLessonsBefore = $crawlerCourse->filter('.btn-link')->count();
        $addButtonCheck = $crawlerCourse->filter('Добавить урок')->count();
        $this->assertSame(0, $addButtonCheck);
    }
    public function testEditLessonNoAdmin()
    {
        $client = $this->authClient('test@gmail.com', 'aaaaaa');
        $client->request('GET', '/courses/');
        $crawlerCourse = $client->clickLink('Перейти к курсу');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $countOfLessonsBefore = $crawlerCourse->filter('.btn-link')->count();
        $addButtonCheck = $crawlerCourse->filter('Редактировать')->count();
        $this->assertSame(0, $addButtonCheck);
    }
    public function testDeleteLessonNoAdmin()
    {
        $client = $this->authClient('test@gmail.com', 'aaaaaa');
        $client->request('GET', '/courses/');
        $crawlerCourse = $client->clickLink('Перейти к курсу');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $countOfLessonsBefore = $crawlerCourse->filter('.btn-link')->count();
        $addButtonCheck = $crawlerCourse->filter('удалить')->count();
        $this->assertSame(0, $addButtonCheck);
    }
    public function testShowLessonNoPaid()
    {
        $client = $this->authClient('test@gmail.com', 'aaaaaa');
        $crawler=$client->request('GET', '/courses/kurs-veb-razrabotki-dlya-novichkov');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $link = $crawler->filter('.btn-link')->eq(1);
        $client->clickLink($link->text());
        $this->assertContains('Курс не оплачен', $client->getResponse()->getContent());
    }
    public function testShowLessonPaid()
    {
        $client = $this->authClient('test@gmail.com', 'aaaaaa');
        $crawler=$client->request('GET', '/courses/kurs-programmirovaniya-na-c');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $link = $crawler->filter('.btn-link')->eq(1);
        $crawler = $client->clickLink($link->text());
        $addPaidCheck = $crawler->filter('Курс не оплачен')->count();
        $this->assertSame(0, $addPaidCheck);
    }

}
