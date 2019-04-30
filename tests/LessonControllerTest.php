<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\DataFixtures\CourseFixtures;
class LessonControllerTest extends AbstractTest
{
    public function getFixtures(): array
    {
        return [CourseFixtures::class];
    }

    public function testLessonsPage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/lessons/');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }
    public function testNewLessonPage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/courses/');
        $client->clickLink('Перейти к курсу');
        $client->clickLink('Добавить урок');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }
    public function testShowLesson()
    {
        $client = static::createClient();
        $client->request('GET', '/courses/');
        $crawler = $client->clickLink('Перейти к курсу');
        $link = $crawler->filter('.btn-link')->first();
        $crawler = $client->click($link->link());
        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }
    public function testAddNewLesson()
    {
        $client = static::createClient();
        $client->request('GET', '/courses/');
        $client->clickLink('Перейти к курсу');
        $crawler = $client->clickLink('Добавить урок');
        $newLessonForm = $crawler->selectButton('Сохранить')->form();
        $newLessonForm["lesson[title]"] = "Это тестовый урок";
        $newLessonForm["lesson[content]"] = "Это описание тестового урока";
        $newLessonForm["lesson[serialNumber]"] = "2";
        $client->submit($newLessonForm);
        $client->request('GET', '/courses/');
        $crawler = $client->clickLink('Перейти к курсу');
        $this->assertEquals(4, $crawler->filter('.btn-link')->count());
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
    /*
     * Не работает
    public function testEditLesson()
    {
        $this->testAddNewLesson();
        $client = static::createClient();
        $client->request('GET', '/courses/');
        $crawler = $client->clickLink('Перейти к курсу');
        $link = $crawler->filter('.btn-link')->first();
        $crawler = $client->click($link->link());
        $client->clickLink('Редактировать');
        $newLessonForm = $crawler->selectButton('Сохранить')->form();
        $newLessonForm["lesson[title]"] = "Это тестовый урок2";
        $newLessonForm["lesson[content]"] = "Это описание тестового урока2";
        $newLessonForm["lesson[serialNumber]"] = "3";
        $client->submit($newLessonForm);
        $crawler = $client->request('GET', '/courses/');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }*/
    public function deleteLesson()
    {
        $client = static::createClient();
        $client->request('GET', '/courses/');
        $crawler = $client->clickLink('Перейти к курсу');
        $links = $crawler->filter('lesson')->links();
        $crawler = $client->click($links[0]);
        $deleteLessonForm = $crawler->selectButton('Удалить')->form();
        $client->submit($deleteLessonForm);
        $crawler = $client->request('GET', '/courses/');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

}
