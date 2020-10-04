<?php

declare(strict_types=1);

namespace Pfazzi\Blockchain\Sodium;

use Pfazzi\Blockchain\SignatureVerifier;

use function hex2bin;
use function sodium_crypto_sign_verify_detached;

class SodiumSignatureVerifier implements SignatureVerifier
{
    public function verify(string $signature, string $message, string $publicKeyHex): bool
    {
        return sodium_crypto_sign_verify_detached(
            $signature,
            $message,
            hex2bin($publicKeyHex)
        );
    }
}
