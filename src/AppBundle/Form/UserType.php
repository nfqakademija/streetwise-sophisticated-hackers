<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * Class UserType
 * @package AppBundle\Form
 */
class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'email',
                EmailType::class
            )
            ->add(
                'plainPassword',
                PasswordType::class,
                [
                    'required'    => false,
                ]
            )
            ->add(
                'confirmPassword',
                PasswordType::class,
                [
                    'required'    => false,
                ]
            )
            ->add(
                'save',
                SubmitType::class
            )
        ;
    }
}
