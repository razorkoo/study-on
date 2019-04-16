<?php

namespace App\DataFixtures;

use App\Entity\Course;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class CourseFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        $courseOne = new Course();
        $courseOne->setTitleCourse("Курс мобильной разработки");
        $courseOne->setDescriptionCourse("В рамках данного курса Вы научитесь разрабатывать мобильные приложения под любую платформу");
        $manager->persist($courseOne);
        $courseTwo = new Course();
        $courseTwo->setTitleCourse("Курс веб разработки");
        $courseTwo->setDescriptionCourse("В рамках данного курса Вы научитесь разрабатывать веб приложения");
        $manager->persist($courseTwo);
        $courseThree = new Course();
        $courseThree->setTitleCourse("Курс программирования на c++");
        $courseThree->setDescriptionCourse("В рамках данного курса Вы научитесь разрабатывать приложения на языке с++");
        $manager->persist($courseThree);


        $manager->flush();
    }
}
