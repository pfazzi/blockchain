<?php

declare(strict_types=1);

namespace Pfazzi\Blockchain\HashAlgorithm;

use Pfazzi\Blockchain\HashAlgorithm;

use function hash;

class SHA3256 implements HashAlgorithm
{
    public function calculateHash(string $message): string
    {
        return hash('sha3-256', $message);
    }
}
