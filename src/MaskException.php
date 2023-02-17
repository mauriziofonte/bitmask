<?php

declare(strict_types=1);

namespace Mfonte\Bit;

final class MaskException extends \Exception
{
    public static function whenFlagIsPresentInMask(Mask $mask, int $flag): self
    {
        return new self(sprintf('The flag %032b is already present in mask %032b', $flag, $mask->getAll()));
    }

    public static function whenFlagIsAbsentInMask(Mask $mask, int $flag): self
    {
        return new self(sprintf('The flag %032b is absent in mask %032b', $flag, $mask->getAll()));
    }

    public static function whenMaskIsNegative(Mask $mask): self
    {
        return new self(sprintf('The mask %032b must be a positive integer', $mask->getAll()));
    }
}
