<?php

namespace Matmar10\Money\CurrencyExchange\Entity;

use Iterator;
use Matmar10\Money\Entity\CurrencyInterface;
use Matmar10\Money\Entity\ExchangeRateInterface;
use Matmar10\Money\Entity\MoneyInterface;
use Matmar10\Money\CurrencyExchange\Entity\MarketDepthPriceInterface;

interface MarketDepthInterface extends Iterator
{

    /**
     * Adds a market depth price point to the market depth collection
     *
     * @abstract
     * @param \Matmar10\Money\CurrencyExchange\Entity\MarketDepthPriceInterface $marketDepthPrice
     * @return null
     */
    public function addMarketDepthPrice(MarketDepthPriceInterface $marketDepthPrice);

}
