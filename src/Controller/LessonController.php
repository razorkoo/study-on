<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\Lesson;
use App\Form\Course1Type;
use App\Form\LessonType;
use App\Repository\LessonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/lessons")
 */
class LessonController extends AbstractController
{
    /**
     * @Route("/", name="lesson_index", methods={"GET"})
     */
    public function index(LessonRepository $lessonRepository): Response
    {
        return $this->render('lesson/index.html.twig', [
            'lessons' => $lessonRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="lesson_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $course_id = $request->query->get('course_id');
        $myCourse =  $this->getDoctrine()->getRepository(Course::class)->find($course_id);

        if($myCourse){
            $lesson = new Lesson();
            $lesson->setLessonCourse($myCourse);
            $form = $this->createForm(LessonType::class, $lesson);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($lesson);
                $entityManager->flush();
                $response = $this->forward('App\Controller\CourseController::show', [
                    'id'  => $course_id
                ]);
                return $response;
            }
        }



        return $this->render('lesson/new.html.twig', [
            'lesson' => $lesson,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="lesson_show", methods={"GET"})
     */
    public function show(Lesson $lesson): Response
    {
        return $this->render('lesson/show.html.twig', [
            'lesson' => $lesson,
            'course'=>$lesson->getLessonCourse(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="lesson_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Lesson $lesson): Response
    {
        $form = $this->createForm(LessonType::class, $lesson);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('lesson_index', [
                'id' => $lesson->getId(),
            ]);
        }

        return $this->render('lesson/edit.html.twig', [
            'lesson' => $lesson,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="lesson_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Lesson $lesson): Response
    {
        $idCourse = $lesson->getLessonCourse()->getId();
        if ($this->isCsrfTokenValid('delete'.$lesson->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($lesson);
            $entityManager->flush();
        }
        $response = $this->forward('App\Controller\CourseController::show', [
            'id'  => $idCourse]);
        return $response;

    }
}
