<?php

namespace App\Form;

use App\Entity\Observation;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;

class ObservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('datetime', DateTimeType::class, [
                'html5' => false,
                'input' => 'string',
                'constraints' => [
                    new NotNull()
                ]
            ])
            ->add('aTemp', NumberType::class, [
                'scale' => 2
            ])
            ->add('bHum', IntegerType::class)
            ->add('bTemp', NumberType::class, [
                'scale' => 2
            ])
            ->add('extHum', IntegerType::class)
            ->add('extTemp', NumberType::class, [
                'scale' => 2
            ])
            ->add('aHum', IntegerType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'data_class' => Observation::class,
        ]);
    }
}
