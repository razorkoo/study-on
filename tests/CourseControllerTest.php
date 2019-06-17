<?php

namespace App\Tests;

use App\DataFixtures\CourseFixtures;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CourseControllerTest extends AbstractTest
{
    public function getFixtures(): array
    {
        return [CourseFixtures::class];
    }

    public function testIndexPage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/courses/');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }
    public function testNewPage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/courses/');
        $client->clickLink('Добавить новый курс');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }
    public function testShowPage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/courses/');
        $client->clickLink('Перейти к курсу');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }
    public function testShowCoursesPage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/courses/');
        $client->clickLink('Перейти к курсу');
        $client->clickLink('К списку курсов');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }
    public function testEditPage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/courses/');
        $client->clickLink('Перейти к курсу');
        $client->clickLink('Редактировать курс');
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
    public function testCountCourses()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/courses/');
        $this->assertEquals(3, $crawler->filter('.card')->count());
    }
    public function testPage404()
    {
        $courseId = 65128;
        $client = static::createClient();
        $crawler = $client->request('GET', "/courses/$courseId");
        $this->assertSame(404, $client->getResponse()->getStatusCode());
    }
    public function testEditPage404()
    {
        $courseId = 404;
        $client = static::createClient();
        $crawler = $client->request('GET', "/courses/$courseId/edit");
        $this->assertSame(404, $client->getResponse()->getStatusCode());
    }
    public function testCreateCoursePage()
    {
        $client = static::createClient();
        $client->request('GET', '/courses/');
        $crawler = $client->clickLink('Добавить новый курс');
        $newCourseForm = $crawler->selectButton('Сохранить')->form();
        $newCourseForm["course[title]"] = "Это тестовый урок";
        $newCourseForm["course[description]"] = "Это описание тестового урока";
        $client->submit($newCourseForm);
        $crawler = $client->request('GET', '/courses/');
        $this->assertEquals(4, $crawler->filter('.card')->count());
    }
    public function testEditCourse()
    {
        $client = static::createClient();
        $client->request('GET', '/courses/');
        $client->clickLink('Перейти к курсу');
        $crawler = $client->clickLink('Редактировать курс');
        $courseForm = $crawler->selectButton('Сохранить')->form();
        $courseForm["course[title]"] = "Это измененный урок";
        $courseForm["course[description]"] = "это изменение описания урока";
        $client->submit($courseForm);
        $crawler = $client->request('GET', '/courses/');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('html:contains("это изменение описания урока")')->count() > 0);
    }
    public function deleteCourse()
    {
        $client = static::createClient();
        $client->request('GET', '/courses/');
        $crawler = $client->clickLink('Добавить новый курс');
        $newCourseForm = $crawler->selectButton('Сохранить')->form();
        $newCourseForm["course[title]"] = "Это тестовый урок2";
        $newCourseForm["course[description]"] = "Это описание тестового урока2";
        $client->submit($newCourseForm);
        $client->request('GET', '/courses/');
        $crawler = $client->clickLink('Перейти к курсу');
        $deleteCourseForm = $crawler->selectButton('Удалить')->form();
        $client->submit($deleteCourseForm);
        $crawler = $client->followRedirect();
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('html:contains("Это тестовый урок2")')->count() === 0);
    }
    public function newCourseWithoutTitle()
    {
        $client = static::createClient();
         $client->request('GET', '/courses/');
        $crawler = $client->clickLink('Добавить новый курс');
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
        $client = static::createClient();
        $client->request('GET', '/courses/');
        $crawler = $client->clickLink('Добавить новый курс');
        $newCourseForm = $crawler->selectButton('Сохранить')->form();
        $newCourseForm["course[title]"] = "тест";
        $newCourseForm["course[description]"] = "";
        $client->submit($newCourseForm);
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Заполните это поле")')->count()
        );
    }
}
