<?php

namespace Matmar10\Money\CurrencyExchange\Entity;

use Matmar10\Money\CurrencyExchange\Entity\CurrencyExchangeOperationInterface;
use Matmar10\Money\CurrencyExchange\Entity\MarketDepthPriceInterface;
use Matmar10\Money\CurrencyExchange\Exception\InvalidArgumentException;
use Matmar10\Money\Entity\ExchangeRate;
use Matmar10\Money\Entity\CurrencyInterface;
use Matmar10\Money\Entity\MoneyInterface;

class MarketDepthPrice extends ExchangeRate implements MarketDepthPriceInterface
{
    /**
     * @var \Matmar10\Money\Entity\MoneyInterface
     */
    protected $depth;

    /**
     * @var string
     */
    protected $type;

    public function __construct(CurrencyInterface $fromCurrency = null, CurrencyInterface $toCurrency = null, $rate = null, $type = null, MoneyInterface $depth = null)
    {
        if(is_null($fromCurrency)
            || is_null($toCurrency)
            || is_null($rate)) {
            return;
        }
        parent::__construct($fromCurrency, $toCurrency, $rate);
        if(is_null($type)) {
            return;
        }
        $this->setType($type);
        if(is_null($depth)) {
            return;
        }
        $this->setDepth($depth);
    }

    /**
     * {inheritDoc}
     */
    public function setDepth(MoneyInterface $depth)
    {
        $depthCurrency = $depth->getCurrency();
        if(!$depthCurrency->equals($this->fromCurrency) && !$depthCurrency->equals($this->toCurrency)) {
            $errorMessage = "Invalid depth provided: depth currency must match either the exchange rate fromCurrency or toCurrency (expected '%s' or '%s', but '%s' provided)";
            throw new InvalidArgumentException(sprintf($errorMessage, $this->fromCurrency, $this->toCurrency, $depthCurrency));
        }
        $this->depth = $depth;
    }

    /**
     * {inheritDoc}
     */
    public function getDepth()
    {
        return $this->depth;
    }

    /**
     * {inheritDoc}
     */
    public function setType($type)
    {
        if(CurrencyExchangeOperationInterface::TYPE_ASK !== $type && CurrencyExchangeOperationInterface::TYPE_BID !== $type) {
            $errorMessage = "Invalid market depth price type '%s' provided (expected %s or %s)";
            throw new InvalidArgumentException(sprintf($errorMessage, $type, CurrencyExchangeOperationInterface::TYPE_ASK, CurrencyExchangeOperationInterface::TYPE_BID));
        }
        $this->type = $type;
    }

    /**
     * {inheritDoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {inheritDoc}
     */
    public function supports(MarketDepthPriceInterface $requestedDepth)
    {
        // need to match BID -> ASK and ASK -> BID to be satisfied
        if($requestedDepth->getType() === $this->type) {
            $errorMessage = "Cannot check if market depth price of type '%s' supports market depth price of type '%s' (only %s orders can satisfy %s orders)";
            throw new InvalidArgumentException(sprintf(
                $errorMessage,
                $requestedDepth->getType(),
                $this->type,
                CurrencyExchangeOperationInterface::TYPE_BID,
                CurrencyExchangeOperationInterface::TYPE_ASK
            ));
        }

        // must be equal or inverse
        if(!$this->equals($requestedDepth) && !$this->isInverse($requestedDepth)) {
            $errorMessage = "Cannot check if market depth price is supported: fromCurrency and toCurrency must match.";
            throw new InvalidArgumentException($errorMessage);
        }

        $thisAmountCurrency = $this->depth->getCurrency();
        $requestedAmountCurrency = $requestedDepth->getDepth()->getCurrency();
        $requestedAmount = $requestedDepth->getDepth();
        if(!$thisAmountCurrency->equals($requestedAmountCurrency)) {
            $requestedAmount = $requestedDepth->convert($requestedAmount);
        }

        if(CurrencyExchangeOperationInterface::TYPE_ASK === $this->type) {
            // request is BID (to buy), so they must outbid or match our asking price
            if($requestedDepth->getMultiplier() >= $this->multiplier
                && $requestedAmount->isLessOrEqual($this->depth)) {
                return true;
            }
            return false;
        }

        // CurrencyExchangeOperationInterface::TYPE_BID === $this->type
        // request is ASK (to sell), so their selling price must be less or matching our buy price
        if($requestedDepth->getMultiplier() <= $this->multiplier
            && $this->depth->isLessOrEqual($requestedAmount)) {
            return true;
        }
        return false;
    }
}
