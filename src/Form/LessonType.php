<?php

namespace App\Form;

use App\Entity\Lesson;
use App\Form\DataTransformer\CourseIdToCourseObject;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LessonType extends AbstractType
{

    private $transformer;

    public function __construct(CourseIdToCourseObject $transformer)
    {
        $this->transformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, array('required' => true, 'attr' => array('class' => 'form-control')))
            ->add('content', TextareaType::class, array('required' => true, 'attr' => array('class' => 'form-control')))
            ->add('serialNumber', IntegerType::class, array('required' => true,
                'attr' => array('class' => 'form-control','type'=>'number')))
            ->add('lessonCourse', HiddenType::class)
        ;
        $builder->get('lessonCourse')
            ->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Lesson::class,
        ]);
    }
}
