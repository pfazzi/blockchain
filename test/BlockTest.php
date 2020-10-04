<?php
declare(strict_types=1);

namespace Pfazzi\Blockchain\Test;

use Pfazzi\Blockchain\Block;
use Pfazzi\Blockchain\Clock;
use Pfazzi\Blockchain\SHA3256;
use Pfazzi\Blockchain\Transaction;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class BlockTest extends TestCase
{
    use ProphecyTrait;

    private Clock $clock;

    protected function setUp(): void
    {
        $this->clock = new Clock();
    }

    public function test_hash_is_calculated_on_creation(): void
    {
        $block = new Block(new SHA3256(), $this->clock->timestamp(), []);

        self::assertNotNull($block->hash());
    }

    public function test_it_is_invalid_if_contains_invalid_transactions(): void
    {
        $validTx = $this->prophesize(Transaction::class);
        $validTx->isValid()->willReturn(true);
        $validTx->jsonSerialize()->willReturn([]);

        $invalidTx = $this->prophesize(Transaction::class);
        $invalidTx->isValid()->willReturn(false);
        $invalidTx->jsonSerialize()->willReturn([]);


        $block = new Block(new SHA3256(), $this->clock->timestamp(), [$validTx->reveal(), $invalidTx->reveal()]);

        self::assertFalse($block->hasValidTransactions());
    }

    public function test_it_can_be_mined(): void
    {
        $block = new Block(new SHA3256(), $this->clock->timestamp(), []);

        $initialHash = $block->hash();

        $block->mineBlock(new SHA3256(), 2);

        self::assertNotEquals($initialHash, $block->hash());
        self::assertStringStartsWith('00', $block->hash());
    }
}