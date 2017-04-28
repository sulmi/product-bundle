<?php

namespace Sulmi\ProductBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Media form.
 *
 * @author    Mirosław Sulowski <mirekprograms@gmail.com>
 */
class ProductMediaType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('picture', FileType::class, [
                    'multiple' => 'true',
                    'label' => false,
                    'required' => false,
                ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Sulmi\ProductBundle\Entity\ProductMedia'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sulmi_productbundle_productmedia';
    }

}