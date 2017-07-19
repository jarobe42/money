<?php

namespace Money\Exchange;

use Exchanger\Exception\Exception as ExchangerException;
use Money\Currency;
use Money\CurrencyPair;
use Money\Exception\UnresolvableCurrencyPairException;
use Money\Exception\UnresolvableHistoricalCurrencyPairException;
use Money\Exchange;
use Swap\Swap;

/**
 * Provides a way to get exchange rate from a third-party source and return a currency pair.
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
final class SwapExchange implements Exchange
{
    /**
     * @var Swap
     */
    private $swap;

    /**
     * @param Swap $swap
     */
    public function __construct(Swap $swap)
    {
        $this->swap = $swap;
    }

    /**
     * {@inheritdoc}
     */
    public function quote(Currency $baseCurrency, Currency $counterCurrency)
    {
        try {
            $rate = $this->swap->latest($baseCurrency->getCode().'/'.$counterCurrency->getCode());
        } catch (ExchangerException $e) {
            throw UnresolvableCurrencyPairException::createFromCurrencies($baseCurrency, $counterCurrency);
        }

        return new CurrencyPair($baseCurrency, $counterCurrency, $rate->getValue());
    }

    /**
     * @param \DateTime $historicalDate
     * @param Currency $baseCurrency
     * @param Currency $counterCurrency
     * @return CurrencyPair
     */
    public function quoteHistorical(\DateTime $historicalDate, Currency $baseCurrency, Currency $counterCurrency)
    {
        try {
            $rate = $this->swap->historical($baseCurrency->getCode().'/'.$counterCurrency->getCode(), $historicalDate);
        } catch (ExchangerException $e) {
            throw UnresolvableHistoricalCurrencyPairException::createFromCurrenciesAndDateTime(
                $baseCurrency,
                $counterCurrency,
                $historicalDate
            );
        }

        return new CurrencyPair($baseCurrency, $counterCurrency, $rate->getValue());
    }
}
