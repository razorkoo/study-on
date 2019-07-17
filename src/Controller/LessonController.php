<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\Lesson;
use App\Form\Course1Type;
use App\Form\LessonType;
use App\Repository\LessonRepository;
use App\Service\BillingClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/lessons")
 */
class LessonController extends AbstractController
{
    /*
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
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function new(Request $request): Response
    {
        $courseId = $request->query->get('course_id');
        $newCourse =  $this->getDoctrine()->getRepository(Course::class)->find($courseId);

        if ($newCourse) {
            $lesson = new Lesson();
            $lesson->setLessonCourse($newCourse);
            $form = $this->createForm(LessonType::class, $lesson);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();

                $entityManager->persist($lesson);
                $newCourse->addLesson($lesson);
                $entityManager->persist($newCourse);
                $entityManager->flush();
                $response = $this->forward('App\Controller\CourseController::show', [
                    'id'  => $courseId
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
     * @IsGranted("ROLE_USER")
     */
    public function show(Lesson $lesson,  BillingClient $billingClient): Response
    {
        $user = $this->getUser();
        if($user) {
            $getPayInformation = $billingClient->getTransactions($user->getToken(), ['course_code' => $lesson->getLessonCourse()->getSlug()]);
            //print(count($getPayInformation));
            $billingCourse = $billingClient->getDetailCourseInfo($lesson->getLessonCourse()->getSlug());
            if (array_key_exists('message', $getPayInformation)) {
                $this->addFlash('failed_lesson','Курс не оплачен');
                return $this->render('lesson/show.html.twig', [
                    'error_message'=>'Курс не оплачен'
                ]);
            } else {
                if (count($getPayInformation) <= 0) {
                    return $this->render('lesson/show.html.twig', [
                        'error_message'=>'Курс не оплачен'
                    ]);
                } elseif (count($getPayInformation) > 0 && $billingCourse['type'] == 'rent') {
                    foreach ($getPayInformation as $transaction) {
                        if (array_key_exists('expired_at', $transaction)) {
                            $transaction['expired_at'] = new \DateTime($transaction['expired_at']);
                            if (new \DateTime() > $transaction['expired_at']) {
                                return $this->render('lesson/show.html.twig', [
                                    'error_message'=>'Срок аренды истек'
                                ]);
                            }
                        }
                    }
                }
            }
        } else {
            $this->render('paid.html.twig',['error_message'=>'Вы не авторизованы']);
        }
        return $this->render('lesson/show.html.twig', [
            'lesson' => $lesson,
            'course'=>$lesson->getLessonCourse(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="lesson_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_SUPER_ADMIN")
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
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function delete(Request $request, Lesson $lesson): Response
    {
        $idCourse = $lesson->getLessonCourse();
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
