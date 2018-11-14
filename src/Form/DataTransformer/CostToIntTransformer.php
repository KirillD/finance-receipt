<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class CostToIntTransformer implements DataTransformerInterface
{
    /**
     * @param integer $cost
     * @return float|null
     */
    public function transform($cost)
    {
        if (!$cost) {
            return null;
        }

        return $cost / 100;
    }

    /**
     * @param mixed $cost
     * @return int|
     */
    public function reverseTransform($cost)
    {
        if (null === $cost) {
            return null;
        }

        return (int) ($cost * 100);
    }
}