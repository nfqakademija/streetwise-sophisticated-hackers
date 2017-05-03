<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UserWithPasswordType extends AbstractType
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
                'username',
                TextType::class
            )
            ->add(
                'name',
                TextType::class
            )
            ->add(
                'email',
                EmailType::class
            )
            ->add(
                'occupation',
                TextType::class,
                [
                    'required'    => false,
                ]
            )
            ->add(
                'interests',
                TextType::class,
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
