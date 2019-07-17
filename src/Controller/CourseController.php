<?php

namespace App\Controller;

use App\Entity\Course;
use App\Form\Course1Type;
use App\Form\CourseType;
use App\Entity\CourseModel;
use App\Repository\CourseRepository;
use App\Service\BillingClient;
use DeepCopy\f001\A;
use Doctrine\DBAL\Types\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Cocur\Slugify\Slugify;

/**
 * @Route("/courses")
 */
class CourseController extends AbstractController
{
    /**
     * @Route("/", name="course_index", methods={"GET"})
     */
    public function index(CourseRepository $courseRepository, BillingClient $billingClient): Response
    {

        $courses = $this->getDoctrine()->getRepository(Course::class)->findAll();
        $coursesBilling = $billingClient->getAllCourses();
        $mergedCourses = [];
        foreach ($courses as $course) {
            foreach ($coursesBilling as $cb) {
                if ($course->getSlug() == $cb['code']) {
                    $courseModel = new CourseModel();
                    $courseModel->course = $course;

                    if ($cb['type'] == 'rent') {
                        $courseModel->type = "Аренда";
                        $courseModel->price = $cb['price'];
                    }
                    if ($cb['type'] == 'full') {
                        $courseModel->type = "Покупка";
                        $courseModel->price = $cb['price'];
                    }
                    if ($cb['type'] == 'free') {
                        $courseModel->type = "Бесплатный";
                        $courseModel->price=0.0;
                    }
                    $mergedCourses[] = $courseModel;
                }
            }
        }

        return $this->render('course/index.html.twig', [
            'courses' => $courses,
            'mergedCourses' => $mergedCourses
        ]);
    }

    /**
     * @Route("/new", name="course_new", methods={"GET","POST"})
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function new(Request $request): Response
    {

        //$form = $this->createForm(Course1Type::class, $course);
        //$form->handleRequest($request);

        $course = new Course();
        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $slugify = new Slugify();
            $slug = $slugify->slugify($course->getTitle());
            $course->setSlug($slug);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($course);
            $entityManager->flush();

            return $this->redirectToRoute('course_index');
        }

        return $this->render('course/new.html.twig', [
            'course' => $course,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{slug}", name="course_show", methods={"GET"})
     */
    public function show($slug, Course $course,  BillingClient $billingClient): Response
    {

        $lessons = $course->getLessons();
        $billingCourse = $billingClient->getDetailCourseInfo($course->getSlug());
        $user = $this->getUser();


        $mergedCourse = new CourseModel();
        if ($user) {
            $mergedCourse->canPay = 'enabled';

            $user_current = $billingClient->getCurrentInformation($user->getToken());
            if ($billingCourse['type']!='free') {
                if ($user_current['balance'] < $billingCourse['price']) {
                    $mergedCourse->canPay = 'disabled';
                }
            }
            $getPayInformation = $billingClient->getTransactions($user->getToken(), ['course_code' => $course->getSlug()]);
            if (array_key_exists('message', $getPayInformation)) {

                $mergedCourse->isPaidByUser = false;
            } else {
                if (count($getPayInformation) > 0 && ($billingCourse['type'] == 'full' || $billingCourse['type'] == 'free')) {
                    $mergedCourse->isPaidByUser = true;
                } elseif (count($getPayInformation) > 0 && $billingCourse['type'] == 'rent') {
                    foreach ($getPayInformation as $transaction) {
                        if (array_key_exists('expired_at', $transaction)) {
                            $transaction['expired_at'] = new \DateTime($transaction['expired_at']);
                            if (new \DateTime() < $transaction['expired_at']) {
                                $mergedCourse->isPaidByUser = true;
                            }
                        }
                    }
                }
            }
        }
        //print($getPayInformation['message']);

        $mergedCourse->course = $course;
        if ($billingCourse['type'] == 'rent') {
            $mergedCourse->type = "Аренда";
            $mergedCourse->price = $billingCourse['price'];
        }
        if ($billingCourse['type'] == 'full') {
            $mergedCourse->type = "Покупка";
            $mergedCourse->price = $billingCourse['price'];
        }
        if ($billingCourse['type'] == 'free') {
            $mergedCourse->type = "Бесплатный";
            $mergedCourse->price = 0.0;
        }

        return $this->render('course/show.html.twig', [
            'course' => $course,
            'lessons'=>$lessons,
            'mergedCourse' => $mergedCourse
        ]);
    }

    /**
     * @Route("/{slug}/edit", name="course_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function edit(Request $request, Course $course): Response
    {
        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $slugify = new Slugify();
            $slug = $slugify->slugify($course->getTitle());
            $course->setSlug($slug);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($course);
            $entityManager->flush();
            return $this->redirectToRoute('course_index', [
                'id' => $course->getId(),
            ]);
        }

        return $this->render('course/edit.html.twig', [
            'course' => $course,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{slug}", name="course_delete", methods={"DELETE"})
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function delete(Request $request, Course $course): Response
    {
        if ($this->isCsrfTokenValid('delete'.$course->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($course);
            $entityManager->flush();
        }

        return $this->redirectToRoute('course_index');
    }
    /**
     * @Route("/pay/{slug}", name="course_pay", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function payCourse($slug, Request $request, Course $course, BillingClient $billingClient): Response
    {
        $user = $this->getUser();
        $payResult = $billingClient->payCourse($slug, $user->getToken());

        if (array_key_exists("success", $payResult)) {
            $this->addFlash("success", "Курс успешно куплен");
        } else {
            $this->addFlash("failed", $payResult['message']);
        }
        return $this->redirect("/courses/$slug");
    }
}
