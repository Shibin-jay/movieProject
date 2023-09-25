<?php

namespace App\Form;

use App\Entity\Movie;
use App\Entity\Rating;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;

class RatingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//            ->add('ratingScore')
            ->add('ratingScore', null, [
                'constraints' => [
                    new LessThanOrEqual(['value' => 10, 'message' => 'Rating score must be less than or equal to 10.']),
                ],
            ])

            ->add('review')
            ->add('movie', EntityType::class, [
                'class' => Movie::class,
                'choice_label' => 'title',
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Rating::class,
        ]);
    }
}
