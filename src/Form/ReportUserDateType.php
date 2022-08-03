<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\WorkTime;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ReportUserDateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user', EntityType::class, [
                'label' => 'Pracownik',
                'class' => User::class,
                'choice_label' => function($value) {
                    return $value->getFirstName().' '.$value->getlastName();
                },
                'choice_value' => 'id',
                'query_builder' => function(EntityRepository $repo) {
                    $builder = $repo->createQueryBuilder('user');
                    $builder->where('user.active = 1');
                    return $builder->andWhere("user.email <> 'admin@elbitech.pl'");
                },

            ])
            ->add('work_date_start', DateType::class, [
                'mapped' => false,
                'label' => 'Data rozpoczęcia',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'html5' => true,
            ], ['constraints' => [
                new Constraints\NotBlank(),
                new Constraints\DateTime(),
            ],
        ])
            ->add('work_date_end', DateType::class, [
                'mapped' => false,
                'label' => 'Data zakończenia',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'html5' => true,
            ], [
                'constraints' => [
                    new Constraints\NotBlank(),
                    new Constraints\DateTime(),
                    new Constraints\Callback(function($object, ExecutionContextInterface $context) {
                        $start = $context->getRoot()->getData()['work_date_start'];
                        $stop = $object;
        
                        if (is_a($start, \DateTime::class) && is_a($stop, \DateTime::class)) {
                            if ($stop->format('U') - $start->format('U') < 0) {
                                $context
                                    ->buildViolation('Stop must be after start')
                                    ->addViolation();
                            }
                        }
                    }),
                ],])
            ->add('submit', SubmitType::class, ['label' => 'Pokaż', 'attr' => ['class' => 'btn btn-primary mt-3']])
            ->add('pdf', SubmitType::class, ['label' => 'Raport Pełny', 'attr' => ['class' => 'btn btn-success mt-3']])
            ->add('pdfSimple', SubmitType::class, 
            [
                'label' => 'Raport Uproszczony', 
                'attr' => ['class' => 'btn btn-success mt-3']
            ])
        ;

        $builder->get('work_date_start')->addModelTransformer(new CallbackTransformer(
            function ($value) {
                if(!$value) {
                    return new \DateTime('first day of this month');
                }
                return $value;
            },
            function ($value) {
                return $value;
            }
        ));

        $builder->get('work_date_end')->addModelTransformer(new CallbackTransformer(
            function ($value) {
                if(!$value) {
                    return new \DateTime('last day of this month');
                }
                return $value;
            },
            function ($value) {
                return $value;
            }
        ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => WorkTime::class,
        ]);
    }
}
