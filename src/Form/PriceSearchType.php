<?php

namespace App\Form;

use App\Entity\PriceSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class PriceSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('minPrice', IntegerType::class, [
                'required' => false,
                'label' => 'Prix min'
            ])
            ->add('maxPrice', IntegerType::class, [
                'required' => false,
                'label' => 'Prix max'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PriceSearch::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }
}
