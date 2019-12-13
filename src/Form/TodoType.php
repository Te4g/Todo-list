<?php

namespace App\Form;

use App\Entity\Todo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TodoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('detail', null, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Cliquez sur modifier pour détailler votre todo !',
                ]
            ])
            ->add('end_at')
            ->add('is_completed')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Todo::class,
            'allow_add' => true
        ]);
    }
}
