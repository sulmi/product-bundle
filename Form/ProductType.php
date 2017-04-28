<?php

namespace Sulmi\ProductBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Product type.
 *
 * @author    Mirosław Sulowski <mirekprograms@gmail.com>
 */
class ProductType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('price', MoneyType::class, [
                    'attr' => [
                        'class' => 'form-control',
                        'style' => 'float:left;',
                    ],
                    'label' => 'pr.form.lab.price',
                    'currency' => 'PLN',
                ])
                ->add('name', null, [
                    'attr' => [
                        'class' => 'form-control',
                    ],
                    'label' => 'pr.form.lab.name',
                ])
                ->add('description', TextareaType::class, [
                    'attr' => [
                        'style' => 'min-height:300px',
                        'class' => 'tinymce',
                        'data-theme' => 'advanced' // simple, advanced, bbcode
                    ],
                    'label' => 'pr.form.lab.description',
                    
                        ]
                )
//                ->add('categories', null, [
//                    'attr' => [
//                        'class' => 'select form-control'
//                    ]
//                ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Sulmi\ProductBundle\Entity\Product'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sulmi_productbundle_product';
    }

}