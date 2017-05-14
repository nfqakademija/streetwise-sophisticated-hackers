<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

/**
 * Class MessageType
 * @package AppBundle\Form
 */
class MessageType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'subject',
                TextType::class,
                [
                    'constraints' => [
                        new NotBlank(),
                        new Length(array(
                            'max' => 255
                        ))
                    ]
                ]
            )
            ->add(
                'body',
                TextareaType::class,
                [
                    'constraints' => [
                        new NotBlank(),
                        new Length(array(
                            'max' => 255
                        ))
                    ]
                ]
            )
        ;
    }
}
