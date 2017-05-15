<?php

namespace AppBundle\Form;

use AppBundle\Entity\StudentGroup;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserFullType extends UserBigType
{
    /**
     * {@inheritdoc}
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add(
                'enabled',
                CheckboxType::class,
                [
                    'required'    => false,
                    'label' => 'Enabled',
                ]
            )
            ->add(
                'roles',
                ChoiceType::class,
                [
                    'choices' =>
                        [
                            'Student' => 'ROLE_USER',
                            'Lector' => 'ROLE_LECTOR'
                        ],
                    'expanded' => true,
                    'multiple' => true,
                ]
            )
            ->add(
                'plainPassword',
                RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'required'    => false,
                    'constraints' => [
                        new Length(array(
                            // constraint 'min' is not required, because it doubles with
                            // TODO: need to check:
                            // entity->plainPassword constrain
                            // as when constraint 'min' is added, form shows 2 same errors
                            'max' => 72
                        ))
                    ],
                    'options' => array('translation_domain' => 'FOSUserBundle'),
                    'first_options' => array('label' => 'New Password'),
                    'second_options' => array('label' => 'Confirm Password'),
                    'invalid_message' => 'fos_user.password.mismatch',
                ]
            )
            ->add(
                'studentgroup',
                EntityType::class,
                [
                    'required' => false,
                    'class' => StudentGroup::class,
                    'query_builder' => function (EntityRepository $repo) {
                        return $repo->createQueryBuilder('cat')->orderBy('cat.name', 'ASC');
                    }
                ]
            )
        ;
    }

    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => UserFullType::class,
        ));
    }

    public function getName()
    {
        return 'fullUser';
    }
}
