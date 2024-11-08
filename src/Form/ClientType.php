<?php

namespace App\Form;

use App\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ceidgId', TextType::class, [
                'constraints' => [
                    new Length(max: 255)
                ]
            ])
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank(message: 'Name is required'),
                    new Length(max: 255)
                ]
            ])
            ->add('address', TextType::class, [
                'constraints' => [
                    new Length(max: 255)
                ]
            ])
            ->add('city', TextType::class, [
                'constraints' => [
                    new Length(max: 255)
                ]
            ])
            ->add('region', TextType::class, [
                'constraints' => [
                    new Length(max: 255)
                ]
            ])
            ->add('country', TextType::class, [
                'constraints' => [
                    new Length(2)
                ]
            ])
            ->add('postCode', TextType::class, [
                'constraints' => [
                    new Length(max: 255)
                ]
            ])
            ->add('ownerName', TextType::class, [
                'constraints' => [
                    new Length(max: 255)
                ]
            ])
            ->add('ownerSurname', TextType::class, [
                'constraints' => [
                    new Length(max: 255)
                ]
            ])
            ->add('phone', TextType::class, [
                'constraints' => [
                    new Length(max: 255)
                ]
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Email(),
                    new Length(max: 255)
                ]
            ])
            ->add('www', TextType::class, [
                'constraints' => [
                    new Length(max: 255)
                ]
            ])
            ->add('ceidgUrl', TextType::class, [
                'constraints' => [
                    new Length(max: 255)
                ]
            ])
            ->add('taxId', TextType::class, [
                'constraints' => [
                    new Length(max: 255)
                ]
            ])
            ->add('status', TextType::class, [
                'constraints' => [
                    new Length(max: 255)
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Client::class
        ]);
    }
}
