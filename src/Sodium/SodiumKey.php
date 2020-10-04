<?php

declare(strict_types=1);

namespace Pfazzi\Blockchain\Sodium;

use Exception;
use Pfazzi\Blockchain\Key;

use function bin2hex;
use function sodium_crypto_sign_detached;
use function sodium_crypto_sign_keypair;
use function sodium_crypto_sign_publickey;
use function sodium_crypto_sign_secretkey;
use function sodium_crypto_sign_seed_keypair;
use function strlen;

use const SODIUM_CRYPTO_SIGN_SEEDBYTES;

class SodiumKey implements Key
{
    private string $keyPair;

    public function __construct(?string $seed = null)
    {
        if ($seed !== null && strlen($seed) !== SODIUM_CRYPTO_SIGN_SEEDBYTES) {
            throw new Exception('invalid seed length');
        }

        $this->keyPair = $seed ? sodium_crypto_sign_seed_keypair($seed) : sodium_crypto_sign_keypair();
    }

    public function sign(string $txHash): string
    {
        return sodium_crypto_sign_detached($txHash, $this->secretKey());
    }

    public function publicKeyHex(): string
    {
        return bin2hex(sodium_crypto_sign_publickey($this->keyPair));
    }

    private function secretKey(): string
    {
        return sodium_crypto_sign_secretkey($this->keyPair);
    }
}
