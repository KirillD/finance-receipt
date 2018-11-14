<?php

namespace App\Serializer;

use App\Entity\Receipt\ReceiptItem;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class ReceiptItemNormalizer implements NormalizerInterface
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
    public function normalize($receiptItem, $format = null, array $context = array())
    {
        $data['name'] = $receiptItem->getProduct()->getName();
        $data['amount'] = $receiptItem->getAmount();

        return $data;
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($receiptItem, $format = null)
    {
        return $receiptItem instanceof ReceiptItem;
    }
}