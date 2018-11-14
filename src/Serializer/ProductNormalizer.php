<?php

namespace App\Serializer;

use App\Entity\Product\Product;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class ProductNormalizer implements NormalizerInterface
{
    /**
     * @var ObjectNormalizer
     */
    private $normalizer;

    /**
     * ProductNormalizer constructor.
     * @param ObjectNormalizer $normalizer
     */
    public function __construct(ObjectNormalizer $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    /**
     * @inheritdoc
     */
    public function normalize($product, $format = null, array $context = array())
    {
        $data = $this->normalizer->normalize($product, $format, $context);
        $data['cost'] = $product->getCost() / 100;

        return $data;
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Product;
    }
}