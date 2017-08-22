<?php

namespace Money;

/**
 * Parses a string into a Money object.
 *
 * @author Frederik Bosch <f.bosch@genkgo.nl>
 */
interface MoneyParser
{
    /**
     * Parses a string into a Money object (including currency).
     *
     * @param string      $money
     * @param Currency|null $forceCurrency
     *
     * @return Money
     *
     * @throws Exception\ParserException
     */
    public function parse($money, Currency $forceCurrency = null);
}
