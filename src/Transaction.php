<?php

declare(strict_types=1);

namespace Pfazzi\Blockchain;

use Exception;
use JsonSerializable;
use Pfazzi\Blockchain\Sodium\SodiumKey;

use function hash;

class Transaction implements JsonSerializable
{
    private ?string $from;
    private ?string $to;
    private int $amount;
    private ?string $signature = null;

    public function __construct(?string $from, ?string $to, int $amount)
    {
        $this->from   = $from;
        $this->to     = $to;
        $this->amount = $amount;
    }

    public function calculateHash(): string
    {
        $from = $this->from ?? 'null';
        $to   = $this->to ?? 'null';

        return hash('sha3-256', $from . $to . $this->amount);
    }

    public function signTransaction(SodiumKey $key): void
    {
        if ($key->publicKeyHex() !== $this->from()) {
            throw new Exception('You cannot sign transactions for other wallets');
        }

        $txHash = $this->calculateHash();

        $signature = $key->sign($txHash);

        $this->signature = $signature;
    }

    public function isValid(SignatureVerifier $verifier): bool
    {
        if ($this->from === null) {
            return true;
        }

        if ($this->signature === null) {
            throw new Exception('There is no signature in this transaction');
        }

        return $verifier->verify(
            $this->signature,
            $this->calculateHash(),
            $this->from
        );
    }

    public function from(): ?string
    {
        return $this->from;
    }

    public function to(): ?string
    {
        return $this->to;
    }

    public function amount(): int
    {
        return $this->amount;
    }

    public function jsonSerialize()
    {
        return [
            'from' => $this->from,
            'to' => $this->to,
            'amount' => $this->amount,
        ];
    }
}
