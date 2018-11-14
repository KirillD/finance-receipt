<?php

namespace App\Tests\Service;

use App\Entity\Discount;
use App\Entity\Product\Product;
use App\Entity\Receipt\Receipt;
use App\Entity\Receipt\ReceiptItem;
use App\Service\ReceiptService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ReceiptServiceTest extends WebTestCase
{
    /**
     * @var ReceiptService
     */
    private $receiptService;

    public function setUp()
    {
        self::bootKernel();
        $container = self::$container;
        $this->receiptService = $container->get('App\Service\ReceiptService');
    }

    public function testReceiptService()
    {
        $this->assertTrue($this->receiptService instanceof ReceiptService);
    }

    public function testReceiptItemCalculationWithoutDiscount()
    {
        $receiptItem = $this->getReceiptItemWithoutDiscount();
        $data = $this->receiptService->calculateCostForItem($receiptItem);
        $this->assertEquals(2000, $data['cost']);
        $this->assertEquals(120, $data['vatCost']);
        $this->assertEquals(0, $data['discount']);
    }

    public function testReceiptItemCalculationWithDiscount()
    {
        $receiptItem = $this->getReceiptItemWithDiscount();
        $data = $this->receiptService->calculateCostForItem($receiptItem);
        $this->assertEquals(2000, $data['cost']);
        $this->assertEquals(120, $data['vatCost']);
        $this->assertEquals(1000, $data['discount']);
    }

    public function testReceiptCalculationWithDiscount()
    {
        $receipt = $this->getReceiptWithDiscount();
        $data = $this->receiptService->getTotalCostForReceipt($receipt);
        $this->assertEquals(60, $data['vat_6']);
        $this->assertEquals(42, $data['vat_21']);
        $this->assertEquals(1200, $data['total']);
        $this->assertEquals(500, $data['discount']);
    }

    private function getReceiptItemWithoutDiscount()
    {
        $receipt = $this->getRecipt(false);
        $product = $this->getProduct(1000, Product::VAT_CLASS_6);
        $receiptItem = (new ReceiptItem())->setProduct($product)->setReceipt($receipt)->setAmount(2);

        return $receiptItem;
    }

    private function getReceiptItemWithDiscount()
    {
        $receipt = $this->getRecipt(true);
        $product = $this->getProduct(1000, Product::VAT_CLASS_6);
        $receiptItem = (new ReceiptItem())->setProduct($product)->setReceipt($receipt)->setAmount(3);

        return $receiptItem;
    }

    private function getReceiptWithDiscount()
    {
        $receipt = $this->getRecipt(true);
        $product = $this->getProduct(100, Product::VAT_CLASS_21);
        $receiptItem1 = (new ReceiptItem())->setProduct($product)->setAmount(3);
        $product = $this->getProduct(200, Product::VAT_CLASS_6);
        $receiptItem2 = (new ReceiptItem())->setProduct($product)->setAmount(7);
        $receipt->addReceiptItem($receiptItem1);
        $receipt->addReceiptItem($receiptItem2);

        return $receipt;
    }

    private function getRecipt(bool $discount = false)
    {
        $receipt = new Receipt();
        if ($discount) {
            $discount = (new Discount())->setStrategy(Discount::STRATEGY_EVERY_THIRD);
            $receipt->setDiscount($discount);
        }

        return $receipt;
    }

    private function getProduct(int $cost, int $vatType)
    {
        $product = (new Product())->setCost($cost)->setVatClassType($vatType);

        return $product;
    }
}