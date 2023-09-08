<?php

declare(strict_types=1);

namespace App\lib\database;

interface DatabaseInterface
{
    /**
     * @return $this
     */
    function connect(): static;
}
