<?php
/**
 * Created by PhpStorm.
 * User: eleggua
 * Date: 17.4.8
 * Time: 09.24
 */

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class)
            ->add('plainPassword', PasswordType::class, array(
                'required'    => false,
            ))
            ->add('confirmPassword', PasswordType::class, array(
                'required'    => false,
            ))
            ->add('save', SubmitType::class)
        ;
    }
}