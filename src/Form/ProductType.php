<?php

namespace App\Form;

use App\Entity\Product\Product;
use App\Form\DataTransformer\CostToIntTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProductType extends AbstractType
{
    /**
     * @var CostToIntTransformer
     */
    protected $costToIntTransformer;

    /**
     * ProductType constructor.
     * @param CostToIntTransformer $costToIntTransformer
     */
    public function __construct(CostToIntTransformer $costToIntTransformer)
    {
        $this->costToIntTransformer = $costToIntTransformer;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @throws \Exception
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'barcode',
                TextType::class,
                [
                    'constraints' => [
                        new NotBlank(),
                        new Length(['max' => 32])
                    ]
                ]
            )
            ->add(
                'name',
                TextType::class,
                [
                    'constraints' => [
                        new NotBlank(),
                        new Length(['max' => 32])
                    ]
                ]
            )
            ->add(
                'cost',
                NumberType::class,
                [
                    'scale' => 2,
                    'constraints' => [
                        new NotBlank(),
                    ]
                ]
            )
            ->add(
                'vatClassType',
                ChoiceType::class,
                [
                    'choices' => array_flip(Product::VAT_CLASSES),
                    'constraints' => [
                        new NotBlank(),
                    ]
                ]
            );

        $builder->get('cost')->addModelTransformer($this->costToIntTransformer);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Product::class,
                'csrf_protection' => false
            ]
        );
    }
}
