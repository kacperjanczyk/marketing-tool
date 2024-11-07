<?php

namespace App\Form;

use App\Entity\SocialMediaPost;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class SocialMediaPostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank(message: 'Name is required'),
                    new Length(max: 255)
                ]
            ])
            ->add('text', TextType::class, [
                'constraints' => [
                    new NotBlank(message: 'Text is required'),
                    new Length(max: 1000)
                ]
            ])
            ->add('publishDate', IntegerType::class)
            ->add('status', TextType::class, [
                'constraints' => [
                    new Length(max: 255)
                ]
            ])
            ->add('externalPostId', TextType::class, [
                'constraints' => [
                    new Length(max:255)
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SocialMediaPost::class,
            'csrf_protection' => false
        ]);
    }
}
