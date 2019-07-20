<?php

namespace App\Form;

use App\Entity\Course;
use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class CourseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', \Symfony\Component\Form\Extension\Core\Type\TextType::class, array('required' => true, 'attr' => array('class' => 'form-control')))
            ->add('description', TextareaType::class, array('required' => true, 'attr' => array('class' => 'form-control')))
            ->add('type', ChoiceType::class, array('choices' => array('Аренда' => 'rent','Покупка' => 'full','Бесплатный' => 'free'),'required' => true, 'attr' => array('class' => 'form-control')))
            ->add('price', NumberType::class, array('required' => true, 'attr' => array('class' => 'form-control')))
        ;
    }
}
