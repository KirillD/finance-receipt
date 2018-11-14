<?php

namespace App\Entity\Product;

interface VatClassInterface
{
    const VAT_CLASS_6 = 1;
    const VAT_CLASS_21 = 2;
    const VAT_CLASS_6_VALUE = 6;
    const VAT_CLASS_21_VALUE = 21;

    const VAT_CLASSES = [
        self::VAT_CLASS_6 => self::VAT_CLASS_6_VALUE,
        self::VAT_CLASS_21 => self::VAT_CLASS_21_VALUE,
    ];

    /**
     * @return int|null
     */
    public function getVatClassType(): ?int;

    /**
     * @param int|null $type
     * @return self
     */
    public function setVatClassType(?int $type);
}
