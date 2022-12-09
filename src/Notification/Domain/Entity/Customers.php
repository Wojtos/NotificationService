<?php

namespace App\Notification\Domain\Entity;

use ArrayIterator;
use Iterator;
use IteratorAggregate;

/** @implements IteratorAggregate<Customer> */
final class Customers implements IteratorAggregate
{
    /** @param array<Customer> $customers */
    public function __construct(
        private readonly array $customers
    ) {
    }

    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->customers);
    }
}
