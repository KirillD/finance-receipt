<?php

namespace App\Service;

use App\Entity\Product\Product;
use App\Entity\Receipt\Receipt;
use App\Entity\Receipt\ReceiptItem;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class ReceiptService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * ProductService constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->em = $entityManager;
    }

    /**
     * @param Receipt $receipt
     * @param array $data
     * @return Receipt
     * @throws Exception
     */
    public function updateReceipt(Receipt $receipt, array $data)
    {
        $receiptItem = null;
        //Search receipt for existing item
        /** @var ReceiptItem $item */
        foreach ($receipt->getReceiptItems() as $item) {
            if ($item->getProduct()->getBarcode() == $data['barcode']) {
                $receiptItem = $item;
                break;
            }
        }

        if (!$receiptItem) {
            //if operation remove and item doesn't exist
            if ($data['amount'] == 0) {
                throw new Exception('Receipt doesn\'t contain product', Response::HTTP_BAD_REQUEST);
            }
            $product = $this->em->getRepository(Product::class)->findOneBy(['barcode' => $data['barcode']]);
            if (!$product) {
                throw new Exception('product not found', Response::HTTP_NOT_FOUND);
            }

            $receiptItem = (new ReceiptItem())
                ->setProduct($product);
            $this->em->persist($receiptItem);
            $receipt->addReceiptItem($receiptItem);
        }

        if ($data['amount'] === 0) {
            $receipt->removeReceiptItem($receiptItem);
            $this->em->remove($receiptItem);
        } else {
            $receiptItem->setAmount($data['amount']);
        }

        $this->em->flush();

        return $receipt;
    }

    /**
     * @param Receipt $receipt
     * @return Receipt
     */
    public function finishReceipt(Receipt $receipt)
    {
        $receipt->setStatus(Receipt::STATUS_FINISHED);
        $this->em->flush();
        //Place for dispatch some event related with operation

        return $receipt;
    }

    /**
     * @param ReceiptItem $receiptItem
     * @return array
     */
    public function calculateCostForItem(ReceiptItem $receiptItem)
    {
        $product = $receiptItem->getProduct();
        $vatValue = Product::VAT_CLASSES[$product->getVatClassType()];
        $cost = $receiptItem->getAmount() * $product->getCost();
        $vatCost = $cost * $vatValue / 100;

        return [
            'cost' => (int) $cost,
            'vatCost' => (int) $vatCost,
            'vatClass' => $product->getVatClassType(),
        ];
    }

    /**
     * @param Receipt $receipt
     * @return array
     */
    public function getTotalCostForReceipt(Receipt $receipt)
    {
        $response = [
            'vat_6' => 0,
            'vat_21' => 0,
            'total' => 0,
        ];
        foreach ($receipt->getReceiptItems() as $item) {
            $item = $this->calculateCostForItem($item);
            if ($item['vatClass'] == Product::VAT_CLASS_6) {
                $response['vat_6'] += $item['vatCost'];
            }
            if ($item['vatClass'] == Product::VAT_CLASS_21) {
                $response['vat_21'] += $item['vatCost'];
            }
            $response['total'] += $item['cost'];
        }

        return $response;
    }
}
