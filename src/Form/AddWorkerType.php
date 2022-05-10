<?php

namespace App\Form;

use App\Entity\Employ;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddWorkerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('first_name', TextType::class, [
                'label' => 'Imię'
            ])
            ->add('last_name', TextType::class, [
                'label' => 'Nazwisko'
            ])
            ->add('employ', EntityType::class, [
                'label' => 'Sposób zatrudnienia',
                'class' => Employ::class,
                'choice_label' => function($value) {
                    return $value->getName();
                },
                'choice_value' => 'id'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
