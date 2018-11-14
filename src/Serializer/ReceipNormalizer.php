<?php

namespace App\Serializer;

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
        $data['total'] = $this->receiptService->getTotalCostForReceipt($receipt);

        return $data;
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($receipt, $format = null)
    {
        return $receipt instanceof Receipt;
    }
}