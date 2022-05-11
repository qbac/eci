<?php

namespace App\Form;

use App\Entity\Employ;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

class UserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'label' => 'Hasło',
                'mapped' => false,
                'required' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Hasło musi mieć minimum {{ limit }} znaków',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
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
            ->add('cost_hour', MoneyType::class, [
                'label' => 'Stawka godzinowa',
                'currency' => 'PLN',
                'scale' => 2,
                'required' => false
            ])
            ->add('active', CheckboxType::class, [
                'label' => 'Aktywny',
                'required' => false
                //'attr' => array('checked' => 'checked')
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
