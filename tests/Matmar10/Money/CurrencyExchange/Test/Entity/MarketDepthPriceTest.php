<?php

namespace Matmar10\Money\CurrencyExchange\Test\Entity;

use Matmar10\Money\CurrencyExchange\Entity\CurrencyExchangeOperationInterface;
use Matmar10\Money\CurrencyExchange\Entity\MarketDepthPrice;
use Matmar10\Money\Entity\Currency;
use Matmar10\Money\Entity\Money;
use PHPUnit_Framework_TestCase;

class MarketDepthPriceTest extends PHPUnit_Framework_TestCase
{

    public $usd;
    public $btc;
    public $eur;

    public function setUp()
    {
    }

    /**
     * @dataProvider provideTestSettersAndGettersDepthData
     */
    public function testConstructor(Currency $from, Currency $to, $rate, $type, Currency $depthCurrency, $depthAmountFloat, $exception)
    {
        if($exception) {
            $this->setExpectedException($exception);
        }
        $depth = new Money($depthCurrency);
        $depth->setAmountFloat($depthAmountFloat);
        $marketDepthPrice = new MarketDepthPrice($from, $to, $rate, $type, $depth);
        $this->assertInstanceOf('Matmar10\\Money\\Entity\\MoneyInterface', $marketDepthPrice->getDepth());
        $this->assertEquals($type, $marketDepthPrice->getType());
    }

    /**
     * @dataProvider provideTestSettersAndGettersDepthData
     */
    public function testSettersAndGettersDepth(Currency $from, Currency $to, $rate, $type, Currency $depthCurrency, $depthAmountFloat, $exception)
    {
        if($exception) {
            $this->setExpectedException($exception);
        }
        $depth = new Money($depthCurrency);
        $depth->setAmountFloat($depthAmountFloat);
        $marketDepthPrice = new MarketDepthPrice($from, $to, $rate);
        $marketDepthPrice->setType($type);
        $marketDepthPrice->setDepth($depth);
        $this->assertInstanceOf('Matmar10\\Money\\Entity\\MoneyInterface', $marketDepthPrice->getDepth());
        $this->assertEquals($type, $marketDepthPrice->getType());
    }

    public function provideTestSettersAndGettersDepthData()
    {
        $usd = new Currency('USD', 2, 2);
        $btc = new Currency('BTC', 8, 8);
        $eur = new Currency('EUR', 2, 2);
        return array(
            array(
                $btc,
                $eur,
                150,
                CurrencyExchangeOperationInterface::TYPE_BID,
                $btc,
                5,
                false,
            ),
            array(
                $btc,
                $eur,
                150,
                CurrencyExchangeOperationInterface::TYPE_ASK,
                $btc,
                5,
                false,
            ),
            array(
                $btc,
                $eur,
                150,
                'foo',
                $btc,
                5,
                'Matmar10\\Money\\CurrencyExchange\\Exception\\InvalidArgumentException',
            ),
            array(
                $btc,
                $eur,
                150,
                CurrencyExchangeOperationInterface::TYPE_BID,
                $usd,
                5,
                'Matmar10\\Money\\CurrencyExchange\\Exception\\InvalidArgumentException',
            ),
        );
    }

    /**
     * @dataProvider provideTestSupportsData
     */
    public function testSupports(
        Currency $from,
        Currency $to,
        $rate,
        $type,
        Currency $depthCurrency,
        $depthAmountFloat,
        Currency $supportsFrom,
        Currency $supportsTo,
        $supportsRate,
        $supportsType,
        $supportsCurrency,
        $supportsAmountFloat,
        $expectedSupportsResult,
        $exception
    )
    {
        if($exception) {
            $this->setExpectedException($exception);
        }
        $depth = new Money($depthCurrency);
        $depth->setAmountFloat($depthAmountFloat);
        $marketDepthPrice = new MarketDepthPrice($from, $to, $rate, $type, $depth);

        $desiredDepthAmount = new Money($supportsCurrency);
        $desiredDepthAmount->setAmountFloat($supportsAmountFloat);
        $desiredDepth = new MarketDepthPrice($supportsFrom, $supportsTo, $supportsRate, $supportsType, $desiredDepthAmount);

        $this->assertEquals($expectedSupportsResult, $marketDepthPrice->supports($desiredDepth));
    }

    public function provideTestSupportsData()
    {
        $usd = new Currency('USD', 2, 2);
        $btc = new Currency('BTC', 8, 8);
        $eur = new Currency('EUR', 2, 2);
        return array(
            array(
                $btc,
                $eur,
                150,
                CurrencyExchangeOperationInterface::TYPE_BID,
                $btc,
                5,
                $btc,
                $eur,
                150,
                CurrencyExchangeOperationInterface::TYPE_ASK,
                $btc,
                5,
                true,
                false,
            ),
            array(
                $btc,
                $eur,
                150,
                CurrencyExchangeOperationInterface::TYPE_BID,
                $btc,
                5,
                $btc,
                $eur,
                150,
                CurrencyExchangeOperationInterface::TYPE_ASK,
                $btc,
                4,
                false,
                false,
            ),
            array(
                $btc,
                $eur,
                100,
                CurrencyExchangeOperationInterface::TYPE_BID,
                $btc,
                5,
                $btc,
                $eur,
                100,
                CurrencyExchangeOperationInterface::TYPE_ASK,
                $eur,
                500,
                true,
                false,
            ),
            array(
                $btc,
                $eur,
                100,
                CurrencyExchangeOperationInterface::TYPE_BID,
                $btc,
                5,
                $btc,
                $eur,
                100,
                CurrencyExchangeOperationInterface::TYPE_ASK,
                $eur,
                499,
                false,
                false,
            ),
            array(
                $btc,
                $eur,
                100,
                CurrencyExchangeOperationInterface::TYPE_BID,
                $btc,
                5,
                $btc,
                $eur,
                101,
                CurrencyExchangeOperationInterface::TYPE_ASK,
                $eur,
                500,
                false,
                false,
            ),
            array(
                $btc,
                $eur,
                100,
                CurrencyExchangeOperationInterface::TYPE_BID,
                $btc,
                5,
                $btc,
                $eur,
                99,
                CurrencyExchangeOperationInterface::TYPE_ASK,
                $eur,
                500,
                true,
                false,
            ),
            array(
                $btc,
                $eur,
                150,
                CurrencyExchangeOperationInterface::TYPE_BID,
                $btc,
                5,
                $btc,
                $eur,
                150,
                CurrencyExchangeOperationInterface::TYPE_ASK,
                $usd,
                6,
                true,
                'Matmar10\\Money\\CurrencyExchange\\Exception\\InvalidArgumentException',
            ),
            array(
                $btc,
                $eur,
                150,
                CurrencyExchangeOperationInterface::TYPE_BID,
                $btc,
                5,
                $btc,
                $eur,
                150,
                CurrencyExchangeOperationInterface::TYPE_BID,
                $usd,
                5,
                true,
                'Matmar10\\Money\\CurrencyExchange\\Exception\\InvalidArgumentException',
            ),
            array(
                $btc,
                $eur,
                150,
                CurrencyExchangeOperationInterface::TYPE_BID,
                $btc,
                5,
                $btc,
                $eur,
                150,
                CurrencyExchangeOperationInterface::TYPE_ASK,
                $usd,
                5,
                true,
                'Matmar10\\Money\\CurrencyExchange\\Exception\\InvalidArgumentException',
            ),
        );
    }
}
