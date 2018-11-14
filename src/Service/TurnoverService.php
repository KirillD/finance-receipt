<?php

namespace App\Service;

use App\Entity\Discount;
use App\Entity\Product\Product;
use App\Entity\Receipt\Receipt;
use App\Entity\Receipt\ReceiptItem;
use App\Entity\TurnoverStats;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\DateTime;

class TurnoverService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var ReceiptService
     */
    private $receiptService;

    /**
     * TurnoverService constructor.
     * @param EntityManagerInterface $entityManager
     * @param ReceiptService $receiptService
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ReceiptService $receiptService
    ) {
        $this->em = $entityManager;
        $this->receiptService = $receiptService;
    }

    public function updateStats()
    {
        $turnover = $this->em->getRepository(TurnoverStats::class)->findLastTurnover();
        $receiptRepo = $this->em->getRepository(Receipt::class);
        if (!$turnover) {
            $receipt = $receiptRepo->getFirstFinishedReceipt();
            if (!$receipt) {
                return;
            }
            /** @var \DateTime $date */
            $date = $receipt->getFinishedAt();

        } else {
            $date = $turnover->getCreatedAt();

        }
        $date->setTime($date->format('H'), 0, 0);
        $dateNow = new \DateTime();
        while ($dateNow->getTimestamp() > $date->getTimestamp()) {
            $receipts = $receiptRepo->findFinishedReceiptsInPeriod(clone $date);
            $turnoverValue = 0;
            if (count($receipts)) {
                foreach ($receipts as $receipt) {
                    $costData = $this->receiptService->getTotalCostForReceipt($receipt);
                    $turnoverValue += $costData['total'];
                 }
            }
            $date->modify('+1 hour');
            $turnover = (new TurnoverStats())
                ->setTurnover($turnoverValue)
                ->setCreatedAt($date);
            $this->em->persist($turnover);
            $this->em->flush();
        }
    }
}
