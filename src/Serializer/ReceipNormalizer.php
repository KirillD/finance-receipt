<?php

namespace App\Serializer;

use App\Entity\Discount;
use App\Entity\Receipt\Receipt;
use App\Service\ReceiptService;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class ReceipNormalizer implements NormalizerInterface
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
    public function normalize($receipt, $format = null, array $context = array())
    {
        $data = $this->normalizer->normalize($receipt, $format, $context);
        $data['total'] = $this->formatTotalData($this->receiptService->getTotalCostForReceipt($receipt));
        if ($receipt->getDiscount()) {
            $data['discount'] = $receipt->getDiscount()->getStrategy() == Discount::STRATEGY_EVERY_THIRD ?
                'Every third is free' :
                null;
        }

        dump($data);die;
        return $data;
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($receipt, $format = null)
    {
        return $receipt instanceof Receipt;
    }

    /**
     * @param array $data
     * @return array
     */
    private function formatTotalData(array $data)
    {
        foreach ($data as $key => $value) {
            $data[$key] = $value / 100;
        }

        return $data;
    }
}