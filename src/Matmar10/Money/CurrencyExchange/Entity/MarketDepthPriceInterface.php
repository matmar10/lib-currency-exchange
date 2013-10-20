<?php

namespace Matmar10\Money\CurrencyExchange\Entity;

use Matmar10\Money\Entity\ExchangeRateInterface;
use Matmar10\Money\Entity\MoneyInterface;

interface MarketDepthPriceInterface extends CurrencyExchangeOperationInterface, ExchangeRateInterface
{

    /**
     * Sets the depth of demand or supply at this exchange rate
     *
     * @abstract
     * @param \Matmar10\Money\Entity\MoneyInterface $depth
     * @return null
     * @throws \Matmar10\Money\CurrencyExchange\Exception\InvalidArgumentException if the depth provided doesn't match one of the exchange rate currencies
     */
    public function setDepth(MoneyInterface $depth);

    /**
     * Returns the depth of demand or suppy at this exchange rate
     *
     * @abstract
     * @return \Matmar10\Money\Entity\MoneyInterface The demand at this exchange rate
     */
    public function getDepth();

    /**
     * Sets the type of this market depth price (CurrencyExchangeOperationInterface::TYPE_ASK or CurrencyExchangeOperationInterface::TYPE_BID)
     *
     * @abstract
     * @param string $type CurrencyExchangeOperationInterface::TYPE_ASK | CurrencyExchangeOperationInterface::TYPE_BID
     * @return \Matmar10\Money\Entity\MoneyInterface The demand at this exchange rate
     * @throws \Matmar10\Money\CurrencyExchange\Exception\InvalidArgumentException if the type is invalid
     */
    public function setType($type);

    /**
     * Gets the type of this market depth price (CurrencyExchangeOperationInterface::TYPE_ASK or CurrencyExchangeOperationInterface::TYPE_BID)
     *
     * @abstract
     * @return string The type of this market depth price (CurrencyExchangeOperationInterface::TYPE_ASK or CurrencyExchangeOperationInterface::TYPE_BID)
     */
    public function getType();

    /**
     * Tests whether the market depth price instance can support the provided depth amount
     *
     * @abstract
     * @param \Matmar10\Money\CurrencyExchange\Entity\MarketDepthPriceInterface $requestedDepth The requested market depth price
     * @return boolean
     * @throws \Matmar10\Money\CurrencyExchange\Exception\InvalidArgumentException if the types are equal since a BID can never satisfy another BID
     */
    public function supports(MarketDepthPriceInterface $requestedDepth);

}
