<?php
declare(strict_types=1);

namespace Pfazzi\Blockchain;

use JsonSerializable;

class Blockchain implements JsonSerializable
{
    /** @var Block[] */
    private array $chain;
    private int $difficulty;
    /** @var Transaction[] */
    private array $pendingTransactions = [];
    private int $miningReward = 100;
    private Clock $clock;
    private HashAlgorithm $hashAlgorithm;
    private SignatureVerifier $signatureVerifier;

    public function __construct(int $difficulty, Clock $clock, HashAlgorithm $hashAlgorithm, SignatureVerifier $signatureVerifier)
    {
        $this->difficulty = $difficulty;
        $this->clock = $clock;
        $this->hashAlgorithm = $hashAlgorithm;
        $this->signatureVerifier = $signatureVerifier;

        $this->chain = [$this->createGenesisBlock()];
    }

    private function createGenesisBlock(): Block
    {
        return new Block($this->hashAlgorithm, $this->clock->timestamp(), []);
    }

    public function latestBlock(): Block
    {
        $lastIndex = array_key_last($this->chain);

        if ($lastIndex === null) {
            throw new \Exception('Empty chain');
        }

        return $this->chain[$lastIndex];
    }

    public function minePendingTransactions(string $miningRewardAddress): void
    {
        $block = new Block($this->hashAlgorithm, $this->clock->timestamp(), $this->pendingTransactions, $this->latestBlock()->hash());
        $block->mineBlock($this->hashAlgorithm, $this->difficulty);

        $this->chain[] = $block;
        $this->pendingTransactions = [new Transaction(null, $miningRewardAddress, $this->miningReward)];
    }

    public function addTransaction(Transaction $transaction): void
    {
        if ($transaction->from() === null || $transaction->to() === null) {
            throw new \Exception('Transaction must include from and to address');
        }

        if (!$transaction->isValid($this->signatureVerifier)) {
            throw new \Exception('Cannot add an invalid transaction to the chain');
        }

        $this->pendingTransactions[] = $transaction;
    }

    public function getBalanceOf(string $address): int
    {
        $balance = 0;

        foreach ($this->chain as $block) {
            foreach ($block->transactions() as $transaction) {
                if ($transaction->from() === $address) {
                    $balance -= $transaction->amount();
                }
                if ($transaction->to() === $address) {
                    $balance += $transaction->amount();
                }
            }
        }

        return $balance;
    }

    public function isValid(): bool
    {
        for ($index = 1; $index < count($this->chain); $index++) {
            $previousBlock = $this->chain[$index - 1];
            $currentBlock = $this->chain[$index];

            if (!$currentBlock->hasValidTransactions($this->signatureVerifier)) {
                return false;
            }

            if ($currentBlock->hash() !== $currentBlock->calculateHash($this->hashAlgorithm)) {
                return false;
            }

            if ($currentBlock->previousHash() !== $previousBlock->hash()) {
                return false;
            }
        }

        return true;
    }

    public function jsonSerialize()
    {
        return [
            'chain' => $this->chain
        ];
    }
}