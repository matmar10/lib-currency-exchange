<?php

namespace Matmar10\Money\CurrencyExchange\Entity;

use Iterator;
use Matmar10\Money\Entity\CurrencyInterface;
use Matmar10\Money\Entity\CurrencyPairInterface;
use Matmar10\Money\Entity\ExchangeRateInterface;
use Matmar10\Money\Entity\MoneyInterface;
use Matmar10\Money\CurrencyExchange\Entity\MarketDepthPriceInterface;

interface MarketDepthCollectionInterface
{

    /**
     * The currency pair represented by this market depth collection
     *
     * @abstract
     * @param \Matmar10\Money\Entity\CurrencyPairInterface $currencyPair The currency pair
     * @return null
     */
    public function setCurrencyPair(CurrencyPairInterface $currencyPair);

    /**
     * Get the currency pair represented by this market depth collection
     *
     * @abstract
     * @return \Matmar10\Money\Entity\CurrencyPairInterface
     */
    public function getCurrencyPair();

    /**
     * Adds a market depth price point to the market depth collection
     *
     * @abstract
     * @param \Matmar10\Money\CurrencyExchange\Entity\MarketDepthPriceInterface $marketDepthPrice
     * @return null
     * @throws \Matmar10\Money\CurrencyExchange\Exception\InvalidArgumentException if the currency pairs are not equal
     */
    public function add(MarketDepthPriceInterface $marketDepthPrice);

    /**
     * Gets the market depth of asks
     *
     * @abstract
     * @return \Matmar10\Money\CurrencyExchange\Entity\MarketDepthPriceInterface[] The array of market depth prices
     */
    public function getAsks();

    /**
     * Gets the market depth of bids
     *
     * @abstract
     * @return \Matmar10\Money\CurrencyExchange\Entity\MarketDepthPriceInterface[] The array of market depth prices
     */
    public function getBids();

    /**
     * Checks whether the specified market depth can be supported from this collection
     *
     * @abstract
     * @param MarketDepthPriceInterface $marketDepthPrice The market depth price to see if this collection can support
     * @return boolean Whether the requested demand can be supported
     */
    public function getSupported(MarketDepthPriceInterface $marketDepthPrice);

}
