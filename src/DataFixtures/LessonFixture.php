<?php

namespace App\DataFixtures;

use App\Entity\Lesson;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class LessonFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
         $course_names = ['Build a Blockchain and a Cryptocurrency from Scratch', 'Java Programming Masterclass for Software Developers', 'MERN Stack Front To Back: Full Stack React, Redux & Node.js'];
         $course_descriptions = ['Build a blockchain and cryptocurrency. Discover the engineering ideas behind technologies like Bitcoin and Ethereum!', 'Learn Java In This Course And Become a Computer Programmer. Obtain valuable Core Java Skills And Java Certification.', 'Build and deploy a social network with Node.js, Express, React, Redux & MongoDB. Learn how to put it all together.'];
         for ($i=0, $iMax = count($course_names); $i< $iMax; $i++) {
             $course = new Course();
             $course->setName($course_names[$i]);
             $course->setDescription($course_descriptions[$i]);
             $manager->persist($course);
         }
         $manager->flush();
        /*$lesson_name = ['Basic Express Setup', 'User API Routes & JWT Authentication', 'Getting Started With React & The Frontend'];
        $lesson_content = 'Building an extensive backend API with Node.js & Express. Protecting routes/endpoints with JWT (JSON Web Tokens)';
        $number = [1, 2, 3];
        for ($i=0, $iMax = count($lesson_name); $i < $iMax; $i++) {
            $course = $manager->getRepository(Course::class)->find(15);
            $lesson = new Lesson();
            $lesson->setCourse($course);
            $lesson->setName($lesson_name[$i]);
            $lesson->setContent($lesson_content);
            $lesson->setSerialNumber($number[$i]);
            $manager->persist($lesson);
        }*/
    }
}
