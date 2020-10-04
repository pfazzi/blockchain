<?php
declare(strict_types=1);

use Pfazzi\Blockchain\Blockchain;
use Pfazzi\Blockchain\Clock;
use Pfazzi\Blockchain\Sodium\SodiumKey;
use Pfazzi\Blockchain\SHA3256;
use Pfazzi\Blockchain\Sodium\SodiumSignatureVerifier;
use Pfazzi\Blockchain\Transaction;

require_once __DIR__.'/../vendor/autoload.php';

$clock = new Clock();
$sha256 = new SHA3256();
$signatureVerifier = new SodiumSignatureVerifier();

$patrickKey = new SodiumKey();
$patrickWalletAddress = $patrickKey->publicKeyHex();

$giulioKey = new SodiumKey();
$giulioWalletAddress = $giulioKey->publicKeyHex();

$daniloKey = new SodiumKey();
$daniloWalletAddress = $daniloKey->publicKeyHex();

$blockchain = new Blockchain(2, $clock, $sha256, $signatureVerifier);

$tx = new Transaction($patrickWalletAddress, $giulioWalletAddress, 10);
$tx->signTransaction($patrickKey);
$blockchain->addTransaction($tx);

$tx = new Transaction($giulioWalletAddress, $daniloWalletAddress, 20);
$tx->signTransaction($giulioKey);
$blockchain->addTransaction($tx);

$blockchain->minePendingTransactions($daniloWalletAddress);

echo json_encode($blockchain, JSON_PRETTY_PRINT);
echo PHP_EOL;
echo "Patrick's balance is " . $blockchain->getBalanceOf($patrickWalletAddress) . PHP_EOL;
echo "Giulio's balance is " . $blockchain->getBalanceOf($giulioWalletAddress) . PHP_EOL;
echo "Danilo's balance is " . $blockchain->getBalanceOf($daniloWalletAddress) . PHP_EOL;

echo "Is chain valid? " . ($blockchain->isValid() ? 'Yes' : 'No') . PHP_EOL;
