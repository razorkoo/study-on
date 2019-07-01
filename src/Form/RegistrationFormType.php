<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class RegistrationFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', TextType::class, array('label' => 'Электронная почта', 'required' => true, 'attr' => array('class' => 'form-control form-control-lg')))
            ->add('password', PasswordType::class, array('constraints' => [new Length(['min' => 6,'minMessage' => 'Пароль должен быть минимум 6 символов','max' => 4096,])], "error_bubbling" => true, 'label' => 'Пароль', 'required' => true,  'attr' => array('class' => 'form-control form-control-lg')))
            ->add('confirmation', PasswordType::class, array('label' => 'Подтверждение пароля', 'required' => true,  'attr' => array('class' => 'form-control form-control-lg')))
            ->add('save', SubmitType::class, array('label' => 'Зарегистрироваться', 'attr' => array('class' => 'btn btn-primary')))
        ;
    }
}
