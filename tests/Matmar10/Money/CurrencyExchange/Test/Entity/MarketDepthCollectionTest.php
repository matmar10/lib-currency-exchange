<?php

namespace Matmar10\Money\CurrencyExchange\Test\Entity;

use Matmar10\Money\CurrencyExchange\Entity\CurrencyExchangeOperationInterface;
use Matmar10\Money\CurrencyExchange\Entity\MarketDepthCollectionInterface;
use Matmar10\Money\CurrencyExchange\Entity\MarketDepthCollection;
use Matmar10\Money\CurrencyExchange\Entity\MarketDepthPrice;
use Matmar10\Money\Entity\CurrencyPair;
use Matmar10\Money\Entity\CurrencyPairInterface;
use Matmar10\Money\Entity\CurrencyInterface;
use Matmar10\Money\Entity\Currency;
use Matmar10\Money\Entity\Money;
use Matmar10\Money\Entity\MoneyInterface;
use PHPUnit_Framework_TestCase;

class MarketDepthPriceTest extends PHPUnit_Framework_TestCase
{

    public $usd;
    public $btc;
    public $eur;

    /**
     * @dataProvider provideTestData
     */
    public function test(
        CurrencyPairInterface $pair,
        CurrencyInterface $depthCurrency,
        array $bids,
        array $asks,
        $desiredRate,
        $desiredType,
        $desiredCurrency,
        $desiredAmountFloat,
        array $expectedSupported,
        $exception
    )
    {

        if($exception) {
            $this->setExpectedException($exception);
        }
        $collection = new MarketDepthCollection();
        $collection->setCurrencyPair($pair);
        foreach($bids as $bidPrice => $bidAmount) {
            $amount = new Money($depthCurrency);
            $amount->setAmountFloat($bidAmount);
            $bid = new MarketDepthPrice(
                $pair->getFromCurrency(),
                $pair->getToCurrency(),
                (float)$bidPrice,
                CurrencyExchangeOperationInterface::TYPE_BID,
                $amount
            );
            $collection->add($bid);
        }
        foreach($asks as $askPrice => $askAmount) {
            $amount = new Money($depthCurrency);
            $amount->setAmountFloat($askAmount);
            $ask = new MarketDepthPrice(
                $pair->getFromCurrency(),
                $pair->getToCurrency(),
                (float)$askPrice,
                CurrencyExchangeOperationInterface::TYPE_ASK,
                $amount
            );
            $collection->add($ask);
        }

        $desiredAmount = new Money($desiredCurrency);
        $desiredAmount->setAmountFloat($desiredAmountFloat);
        $desired = new MarketDepthPrice(
            $pair->getFromCurrency(),
            $pair->getToCurrency(),
            $desiredRate,
            $desiredType,
            $desiredAmount
        );

        $expectedType = (CurrencyExchangeOperationInterface::TYPE_ASK === $desiredType) ?
            CurrencyExchangeOperationInterface::TYPE_BID : CurrencyExchangeOperationInterface::TYPE_ASK;
        $expectedSupportedDepthPrices = array();
        foreach($expectedSupported as $price => $amount) {
            $amountAsMoney = new Money($depthCurrency);
            $amountAsMoney->setAmountFloat($amount);
            $expectedSupportedDepthPrices[$price] = new MarketDepthPrice(
                $pair->getFromCurrency(),
                $pair->getToCurrency(),
                (float)$price,
                $expectedType,
                $amountAsMoney
            );
        }

        $supported = $collection->getSupported($desired);

        $this->assertEquals($expectedSupportedDepthPrices, $supported);

    }

    public function provideTestData()
    {
        $usd = new Currency('USD', 2, 2);
        $btc = new Currency('BTC', 8, 8);
        $eur = new Currency('EUR', 2, 2);
        return array(
            array(
                new CurrencyPair($btc, $eur),
                $btc,
                array(
                    '101' => 1,
                    '102' => 2,
                    '103' => 3,
                    '104' => 4,
                    '105' => 5,
                ),
                array(
                    '106' => 6,
                    '107' => 7,
                    '108' => 8,
                    '109' => 9,
                    '110' => 10,
                ),
                108,
                CurrencyExchangeOperationInterface::TYPE_BID,
                $btc,
                4,
                array(
                    '106' => 6,
                    '107' => 7,
                    '108' => 8,
                ),
                false,
            ),
            array(
                new CurrencyPair($btc, $eur),
                $btc,
                array(
                    '101' => 1,
                    '102' => 2,
                    '103' => 3,
                    '104' => 4,
                    '105' => 5,
                ),
                array(
                    '106' => 6,
                    '107' => 7,
                    '108' => 8,
                    '109' => 9,
                    '110' => 10,
                ),
                104,
                CurrencyExchangeOperationInterface::TYPE_ASK,
                $btc,
                6,
                array(
                    '104' => 4,
                    '105' => 5,
                ),
                false,
            ),
            array(
                new CurrencyPair($btc, $eur),
                $btc,
                array(
                    '101' => 1,
                    '102' => 2,
                    '103' => 3,
                    '104' => 4,
                    '105' => 5,
                ),
                array(
                    '106' => 6,
                    '107' => 7,
                    '108' => 8,
                    '109' => 9,
                    '110' => 10,
                ),
                104,
                CurrencyExchangeOperationInterface::TYPE_ASK,
                $btc,
                1,
                array(),
                false,
            ),
            array(
                new CurrencyPair($btc, $eur),
                $btc,
                array(
                    '101' => 1,
                    '102' => 2,
                    '103' => 3,
                    '104' => 4,
                    '105' => 5,
                ),
                array(
                    '106' => 6,
                    '107' => 7,
                    '108' => 8,
                    '109' => 9,
                    '110' => 10,
                ),
                104,
                CurrencyExchangeOperationInterface::TYPE_ASK,
                $usd,
                1,
                array(),
                'Matmar10\\Money\\CurrencyExchange\\Exception\\InvalidArgumentException',
            ),
        );
    }

}
