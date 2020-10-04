<?php
declare(strict_types=1);

namespace Pfazzi\Blockchain;

interface HashAlgorithm
{
    public function calculateHash(string $message): string;
}