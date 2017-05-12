<?php

namespace AppBundle\Form;

use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

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
                EmailType::class,
                [
                    'constraints' => [
                        new NotBlank(),
                        new Length(array('max' => 180))
                        // TODO: validate email as '<string>@<string>.<string>'
                        // current form validation is different from entity validation
                    ]
                ]
            )
            ->add(
                'occupation',
                TextType::class,
                [
                    'required'    => false,
                    'constraints' => [
                        new Length(array('max' => 255))
                    ]
                ]
            )
            ->add(
                'interests',
                TextType::class,
                [
                    'required'    => false,
                    'constraints' => [
                        new Length(array('max' => 255))
                    ]
                ]
            )
        ;
    }

    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
        ));
    }

    public function getName()
    {
        return 'baseUser';
    }
}
