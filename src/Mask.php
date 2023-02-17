<?php

declare(strict_types=1);

namespace Mfonte\Bit;

use ArrayIterator;
use IteratorAggregate;

class Mask implements IteratorAggregate
{
    public const EMPTY_MASK = 0;

    public const FLAG_1 = 0b00000000000000000000000000000001; // 1
    public const FLAG_2 = 0b00000000000000000000000000000010; // 2
    public const FLAG_3 = 0b00000000000000000000000000000100; // 4
    public const FLAG_4 = 0b00000000000000000000000000001000; // 8
    public const FLAG_5 = 0b00000000000000000000000000010000; // 16
    public const FLAG_6 = 0b00000000000000000000000000100000; // 32
    public const FLAG_7 = 0b00000000000000000000000001000000; // 64
    public const FLAG_8 = 0b00000000000000000000000010000000; // 128
    public const FLAG_9 = 0b00000000000000000000000100000000; // 256
    public const FLAG_10 = 0b00000000000000000000001000000000; // 512
    public const FLAG_11 = 0b00000000000000000000010000000000; // 1024
    public const FLAG_12 = 0b00000000000000000000100000000000; // 2048
    public const FLAG_13 = 0b00000000000000000001000000000000; // 4096
    public const FLAG_14 = 0b00000000000000000010000000000000; // 8192
    public const FLAG_15 = 0b00000000000000000100000000000000; // 16384
    public const FLAG_16 = 0b00000000000000001000000000000000; // 32768
    public const FLAG_17 = 0b00000000000000010000000000000000; // 65536
    public const FLAG_18 = 0b00000000000000100000000000000000; // 131072
    public const FLAG_19 = 0b00000000000001000000000000000000; // 262144
    public const FLAG_20 = 0b00000000000010000000000000000000; // 524288
    public const FLAG_21 = 0b00000000000100000000000000000000; // 1048576
    public const FLAG_22 = 0b00000000001000000000000000000000; // 2097152
    public const FLAG_23 = 0b00000000010000000000000000000000; // 4194304
    public const FLAG_24 = 0b00000000100000000000000000000000; // 8388608
    public const FLAG_25 = 0b00000001000000000000000000000000; // 16777216
    public const FLAG_26 = 0b00000010000000000000000000000000; // 33554432
    public const FLAG_27 = 0b00000100000000000000000000000000; // 67108864
    public const FLAG_28 = 0b00001000000000000000000000000000; // 134217728
    public const FLAG_29 = 0b00010000000000000000000000000000; // 268435456
    public const FLAG_30 = 0b00100000000000000000000000000000; // 536870912
    public const FLAG_31 = 0b01000000000000000000000000000000; // 1073741824
    public const FLAG_32 = 0b10000000000000000000000000000000; // 2147483648

    /**
     * @var int
     */
    protected $mask = self::EMPTY_MASK;

    /**
     * @var bool
     */
    protected $strictMode = true;

    public function __construct(int $mask = self::EMPTY_MASK, bool $strictMode = true)
    {
        $this->set($mask);
        $this->strictMode = $strictMode;
    }

    /**
     * @throws MaskException
     */
    public function set(int $mask)
    {
        if ($mask < 0) {
            throw MaskException::whenMaskIsNegative($this);
        }

        $this->mask = $mask;
    }

    /**
     * @return int[]
     */
    public function getAll(): array
    {
        $flags = [];

        for ($i = 1; $i <= 32; ++$i) {
            if ($this->has($flag = (int) 2 ** ($i - 1))) {
                $flags[] = $flag;
            }
        }

        return $flags;
    }

    public function get(): int
    {
        return $this->mask;
    }

    public function has(int $flag): bool
    {
        return ($this->mask & $flag) === $flag;
    }

    /**
     * @param int[] ...$flags
     */
    public function hasAll(): bool
    {
        $resultingMask = self::EMPTY_MASK;

        // get function arguments
        $flags = \func_get_args();

        // filter out not-integer arguments
        $flags = array_values(array_filter($flags, function ($flag) {
            return \is_int($flag);
        }));

        // calculate resulting mask
        foreach ($flags as $flag) {
            $resultingMask |= $flag;
        }

        return ($this->mask & $resultingMask) === $resultingMask;
    }

    /**
     * @param int[] ...$flags
     */
    public function hasOneOf(): bool
    {
        // get function arguments
        $flags = \func_get_args();

        // filter out not-integer arguments
        $flags = array_values(array_filter($flags, function ($flag) {
            return \is_int($flag);
        }));

        // check if any of the flags is present in the mask
        foreach ($flags as $flag) {
            if ($this->has($flag)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @throws MaskException
     */
    public function add(int $flag)
    {
        if ($this->strictMode && $this->has($flag)) {
            throw MaskException::whenFlagIsPresentInMask($this, $flag);
        }

        $this->set($this->mask | $flag);
    }

    /**
     * @throws MaskException
     */
    public function remove(int $flag)
    {
        if ($this->strictMode && !$this->has($flag)) {
            throw MaskException::whenFlagIsAbsentInMask($this, $flag);
        }

        $this->set($this->mask & ~$flag);
    }

    public function __toString(): string
    {
        return (string) $this->mask;
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->getAll());
    }
}
