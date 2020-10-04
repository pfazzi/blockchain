<?php

declare(strict_types=1);

namespace Pfazzi\Blockchain;

interface Key
{
    public function sign(string $txHash): string;

    public function publicKeyHex(): string;
}
