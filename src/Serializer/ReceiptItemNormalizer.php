<?php

namespace App\Serializer;

use App\Entity\Receipt\ReceiptItem;
use App\Service\ReceiptService;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class ReceiptItemNormalizer implements NormalizerInterface
{
    /**
     * @var ObjectNormalizer
     */
    private $normalizer;

    /**
     * @var ReceiptService
     */
    private $receiptService;

    /**
     * ProductNormalizer constructor.
     * @param ObjectNormalizer $normalizer
     */
    public function __construct(ObjectNormalizer $normalizer, ReceiptService $receiptService)
    {
        $this->normalizer = $normalizer;
        $this->receiptService = $receiptService;
    }

    /**
     * @inheritdoc
     */
    public function normalize($receiptItem, $format = null, array $context = array())
    {
        $data['name'] = $receiptItem->getProduct()->getName();
        $data['amount'] = $receiptItem->getAmount();
        $data['amount'] = $receiptItem->getAmount();
        $costData = $this->receiptService->calculateCostForItem($receiptItem);
        $data['cost'] = $costData['cost'] / 100;
        $data['vat_cost'] = $costData['vatCost'] / 100;
        $data['vat_class'] = $costData['vatClass'];
        $data['discount'] = $costData['discount'] / 100;

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