<?php

namespace Money\Exception;

use Money\Exception;

/**
 * Thrown when a converter does not support historical conversions
 */
final class ConverterException extends \RuntimeException implements Exception
{
}
