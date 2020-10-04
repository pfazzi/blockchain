<?php
declare(strict_types=1);

namespace Pfazzi\Blockchain\Test;

use Pfazzi\Blockchain\Blockchain;
use Pfazzi\Blockchain\Clock;
use Pfazzi\Blockchain\SHA3256;
use Pfazzi\Blockchain\Transaction;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class BlockchainTest extends TestCase
{
    use ProphecyTrait;

    private Blockchain $blockchain;

    protected function setUp(): void
    {
        $this->blockchain = new Blockchain(2, new Clock(), new SHA3256());
    }

    public function test_it_creates_genesis_block(): void
    {
        $genesisBlock = $this->blockchain->latestBlock();

        self::assertEmpty($genesisBlock->transactions());
        self::assertNull($genesisBlock->previousHash());
    }

    public function test_it_allows_to_add_valid_transactions(): void
    {
        $tx = $this->prophesize(Transaction::class);

        $tx->from()->willReturn('from');
        $tx->to()->willReturn('to');
        $tx->isValid()->willReturn(true);

        $tx = $tx->reveal();

        $this->blockchain->addTransaction($tx);

        self::assertTrue(true);
    }

    public function test_it_deny_to_add_invalid_transactions(): void
    {
        $tx = $this->prophesize(Transaction::class);

        $tx->from()->willReturn(null);

        self::expectExceptionMessage('Transaction must include from and to address');
        $this->blockchain->addTransaction($tx->reveal());

        $tx->from()->willReturn('from');
        $tx->to()->willReturn(null);

        self::expectExceptionMessage('Transaction must include from and to address');
        $this->blockchain->addTransaction($tx->reveal());

        $tx->from()->willReturn('from');
        $tx->to()->willReturn('to');
        $tx->isValid()->willReturn(false);

        self::expectExceptionMessage('Cannot add an invalid transaction to the chain');
        $this->blockchain->addTransaction($tx->reveal());
    }
}