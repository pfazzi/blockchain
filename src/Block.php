<?php
declare(strict_types=1);

namespace Pfazzi\Blockchain;

use JsonSerializable;

class Block implements JsonSerializable
{
    private int $timestamp;
    /** @var Transaction[] */
    private array $transactions;
    private string $hash;
    private ?string $previousHash;
    private int $nonce = 0;

    /**
     * @param Transaction[] $transactions
     */
    public function __construct(HashAlgorithm $hashAlgorithm, int $timestamp, array $transactions, ?string $previousHash = null)
    {
        $this->timestamp = $timestamp;
        $this->transactions = $transactions;
        $this->previousHash = $previousHash;

        $this->hash = $this->calculateHash($hashAlgorithm);
    }

    public function mineBlock(HashAlgorithm $algorithm, int $difficulty): void
    {
        $challenge = str_pad('', $difficulty, '0');
        while (substr($this->hash, 0, $difficulty) !== $challenge) {
            $this->nonce++;
            $this->hash = $this->calculateHash($algorithm);
        }
    }

    public function calculateHash(HashAlgorithm $algorithm): string
    {
        return $algorithm->calculateHash($this->timestamp . json_encode($this->transactions) . $this->nonce);
    }

    public function hash(): string
    {
        return $this->hash;
    }

    public function jsonSerialize()
    {
        return [
            'timestamp' => $this->timestamp,
            'data' => $this->transactions,
            'nonce' => $this->nonce,
            'hash' => $this->hash,
            'previousHash' => $this->previousHash,
        ];
    }

    public function previousHash(): ?string
    {
        return $this->previousHash;
    }

    /**
     * @return Transaction[]
     */
    public function transactions(): array
    {
        return $this->transactions;
    }

    public function hasValidTransactions(SignatureVerifier $verifier): bool
    {
        foreach ($this->transactions as $transaction) {
            if (!$transaction->isValid($verifier)) {
                return false;
            }
        }

        return  true;
    }
}
