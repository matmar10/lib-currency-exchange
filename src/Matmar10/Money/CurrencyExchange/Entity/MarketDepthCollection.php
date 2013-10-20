<?php

namespace Matmar10\Money\CurrencyExchange\Entity;

use Matmar10\Money\CurrencyExchange\Entity\CurrencyExchangeOperationInterface;
use Matmar10\Money\CurrencyExchange\Entity\MarketDepthPriceInterface;
use Matmar10\Money\CurrencyExchange\Entity\MarketDepthCollectionInterface;
use Matmar10\Money\CurrencyExchange\Exception\InvalidArgumentException;
use Matmar10\Money\Entity\CurrencyPairInterface;
use Matmar10\Money\Entity\ExchangeRate;
use Matmar10\Money\Entity\MoneyInterface;

class MarketDepthCollection implements MarketDepthCollectionInterface
{

    /**
     * @var \Matmar10\Money\Entity\CurrencyPairInterface
     */
    protected $currencyPair;

    /**
     * @var \Matmar10\Money\CurrencyExchange\Entity\MarketDepthPriceInterface[]
     */
    protected $asks = array();

    /**
     * @var \Matmar10\Money\CurrencyExchange\Entity\MarketDepthPriceInterface[]
     */
    protected $bids = array();

    /**
     * {inheritDoc}
     */
    public function setCurrencyPair(CurrencyPairInterface $currencyPair)
    {
        $this->currencyPair = $currencyPair;
    }

    /**
     * {inheritDoc}
     */
    public function getCurrencyPair()
    {
        return $this->currencyPair;
    }

    /**
     * {inheritDoc}
     */
    public function add(MarketDepthPriceInterface $marketDepthPrice)
    {
        if(!$this->currencyPair->equals($marketDepthPrice)) {
            throw new InvalidArgumentException('Cannot add market depth price to market depth price collection: currency pair must be equal');
        }

        $type = strtolower($marketDepthPrice->getType()) . 's';
        $depthArray =& $this->$type;

        $multiplier = (string)$marketDepthPrice->getMultiplier();
        if(false === array_key_exists($multiplier, $this->$type)) {
            $depthArray[$multiplier] = $marketDepthPrice;
            return;
        }
        /**
         * @var $existingMarketDepthPrice \Matmar10\Money\CurrencyExchange\Entity\MarketDepthPriceInterface
         */
        $existingMarketDepthPrice = $depthArray[$multiplier];
        $existingDepth = $existingMarketDepthPrice->getDepth();
        if(!$existingDepth->getCurrency()->equals($marketDepthPrice->getDepth()->getCurrency())) {
            $unconvertedDepth = $marketDepthPrice->getDepth();
            $depth = $existingMarketDepthPrice->convert($unconvertedDepth);
        } else {
            $depth = $marketDepthPrice->getDepth();
        }
        $existingMarketDepthPrice->setDepth($existingDepth->add($depth));
        $depthArray[$multiplier] = $existingMarketDepthPrice;
    }

    /**
     * {inheritDoc}
     */
    public function getAsks()
    {
        return $this->asks;
    }

    /**
     * {inheritDoc}
     */
    public function getBids()
    {
        return $this->bids;
    }

    /**
     * {inheritDoc}
     */
    public function getSupported(MarketDepthPriceInterface $marketDepthPrice)
    {
        // need to look up opposite (e.g. match BIDS to ASKS and visa versus)
        $typeKey = (CurrencyExchangeOperationInterface::TYPE_ASK === $marketDepthPrice->getType()) ?
            'bids' : 'asks';
        return array_filter($this->$typeKey, function($marketDepthPriceElement) use ($marketDepthPrice) {
            /**
             * @var $marketDepthPriceElement \Matmar10\Money\CurrencyExchange\Entity\MarketDepthPriceInterface
             */
            return $marketDepthPriceElement->supports($marketDepthPrice);
        });
    }
}
