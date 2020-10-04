<?php
declare(strict_types=1);

namespace Pfazzi\Blockchain;

class Clock
{
    public function timestamp(): int
    {
        return (int) (microtime(true) * 1000);
    }
}