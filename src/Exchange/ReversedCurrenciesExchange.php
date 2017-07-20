<?php

namespace Money\Exchange;

use DateTimeInterface;
use Money\Currency;
use Money\CurrencyPair;
use Money\Exception\ConverterException;
use Money\Exception\HistoricalExchangeException;
use Money\Exception\UnresolvableCurrencyPairException;
use Money\Exchange;
use Money\HistoricalExchange;

/**
 * Tries the reverse of the currency pair if one is not available.
 *
 * Note: adding nested ReversedCurrenciesExchange could cause a huge performance hit.
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
final class ReversedCurrenciesExchange implements HistoricalExchange
{
    /**
     * @var Exchange
     */
    private $exchange;

    /**
     * @param Exchange $exchange
     */
    public function __construct(Exchange $exchange)
    {
        $this->exchange = $exchange;
    }

    /**
     * {@inheritdoc}
     */
    public function quote(Currency $baseCurrency, Currency $counterCurrency)
    {
        try {
            return $this->exchange->quote($baseCurrency, $counterCurrency);
        } catch (UnresolvableCurrencyPairException $exception) {
            try {
                $currencyPair = $this->exchange->quote($counterCurrency, $baseCurrency);

                return new CurrencyPair($baseCurrency, $counterCurrency, 1 / $currencyPair->getConversionRatio());
            } catch (UnresolvableCurrencyPairException $inversedException) {
                throw $exception;
            }
        }
    }

    /**
     * Returns a currency pair for the passed currencies with the rate coming from a third-party source at a certain date.
     *
     * @param Currency $baseCurrency
     * @param Currency $counterCurrency
     * @param DateTimeInterface $date
     *
     * @return CurrencyPair
     *
     * @throws ConverterException When the exchange does not support historical exchanges
     */
    public function historical(Currency $baseCurrency, Currency $counterCurrency, DateTimeInterface $date)
    {
        if(!$this->exchange instanceof HistoricalExchange){
            throw new ConverterException("Reversed Currencies Exchange does not support historical rates.");
        }
        return $this->exchange->historical($baseCurrency, $counterCurrency, $date);
    }
}
