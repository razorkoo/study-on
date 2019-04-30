<?php

namespace App\DataFixtures;

use App\Entity\Course;
use App\Entity\Lesson;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class CourseFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $courses = ['Курс мобильной разработки','Курс веб разработки','Курс программирования на c++'];
        $coursesDescription = ['В рамках данного курса Вы научитесь разрабатывать мобильные 
        приложения под любую платформу',
            'В рамках данного курса Вы научитесь разрабатывать веб приложения',
            'В рамках данного курса Вы научитесь разрабатывать приложения на языке с++'];

        $lessons = [['Справочники и спецификации', 'Редакторы для кода', 'Frameworks'],
            ['Справочники и спецификации', 'Редакторы для кода', 'Консоль разработчика'],
            ['IDE','Hello World','STD']];
        $lessonsContent = [['Изучите справочники','Изучите редакторы кода','Изучите разные фреймворки'],
            ['Изучите справочники для веб разработки','Разные редакторы кода для веб','Возможности консоли'],
            ['Разные IDE для разработки под C++','Первое приложение','STD']];
        $countCourses = 3;
        $countLessons = 3;
        for($i=0;$i<$countCourses;$i++){

            $course= new Course();
            $course->setTitle($courses[$i]);
            $course->setDescription($coursesDescription[$i]);
            $manager->persist($course);

            for($j=0;$j<$countLessons;$j++){
                $lesson = new Lesson();
                $lesson->setLessonCourse($course);
                $lesson->setTitle($lessons[$i][$j]);
                $lesson->setContent($lessonsContent[$i][$j]);
                $lesson->setSerialNumber(($j+1));
                $manager->persist($lesson);
            }
        }

        $manager->flush();
    }
}
