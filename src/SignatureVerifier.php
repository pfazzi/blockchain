<?php

declare(strict_types=1);

namespace Pfazzi\Blockchain;

interface SignatureVerifier
{
    public function verify(string $signature, string $message, string $publicKeyHex): bool;
}
