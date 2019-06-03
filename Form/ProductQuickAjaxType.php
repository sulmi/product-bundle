<?php

namespace Sulmi\ProductBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Product type.
 *
 * @author    Mirosław Sulowski <mirekprograms@gmail.com>
 */
class ProductQuickAjaxType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('price', null, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Cena',
                    'value' => '0.01',
                ]
            ])
            ->add('name', null, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Nazwa',
                ]
            ])
            ->add('description', TextareaType::class, [
                    'attr' => [
                        'class' => 'form-control',
                        'style' => 'min-height:400px;',
                        'placeholder' => 'Opis',
                        'required' => false,
                        'value' => '<br>',
                    ]
                ]
            )
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
        return 'sulmi_productbundle_quickproduct';
    }

}