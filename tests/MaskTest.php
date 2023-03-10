<?php

declare(strict_types=1);

namespace Mfonte\Bit;

use PHPUnit\Framework\TestCase;

class MaskTest extends TestCase
{
    public const TEST_MASK = 42;

    public function testConstructorFailsOnInvalidMask()
    {
        $this->setExpectedException(\Mfonte\Bit\MaskException::class);

        new Mask(-1);
    }

    public function testGetter()
    {
        $mask = new Mask(self::TEST_MASK);

        $this->assertSame(self::TEST_MASK, $mask->get());
        $this->assertNotSame((string) self::TEST_MASK, $mask->get());
    }

    public function testToStringBehaviour()
    {
        $mask = new Mask(self::TEST_MASK);

        $this->assertSame((string) self::TEST_MASK, $mask->__toString());
        $this->assertNotSame(self::TEST_MASK, $mask->__toString());
    }

    public function testIfHasFlag()
    {
        $mask = new Mask(Mask::EMPTY_MASK | Mask::FLAG_13);

        $this->assertFalse($mask->has(Mask::FLAG_3));
        $this->assertTrue($mask->has(Mask::FLAG_13));
    }

    public function testIfHasFlagWithoutEmpty()
    {
        $mask = new Mask(Mask::FLAG_13);

        $this->assertFalse($mask->has(Mask::FLAG_3));
        $this->assertTrue($mask->has(Mask::FLAG_13));
    }

    public function testIfHasFlagFromInteger()
    {
        $mask = new Mask(4096);

        $this->assertFalse($mask->has(Mask::FLAG_3));
        $this->assertTrue($mask->has(Mask::FLAG_13));
    }

    public function testIfHasAllFlags()
    {
        $mask = new Mask(Mask::EMPTY_MASK | Mask::FLAG_3 | Mask::FLAG_13 | Mask::FLAG_23);

        $this->assertTrue($mask->hasAll(Mask::FLAG_3, Mask::FLAG_13, Mask::FLAG_23));
        $this->assertTrue($mask->hasAll(Mask::FLAG_3, Mask::FLAG_13));
        $this->assertTrue($mask->hasAll(Mask::FLAG_3));

        $this->assertFalse($mask->hasAll(Mask::FLAG_3, Mask::FLAG_13, Mask::FLAG_27));
    }

    public function testIfHasOneOfFlags()
    {
        $mask = new Mask(Mask::EMPTY_MASK | Mask::FLAG_3 | Mask::FLAG_13 | Mask::FLAG_23);

        $this->assertTrue($mask->hasOneOf(Mask::FLAG_3, Mask::FLAG_13, Mask::FLAG_23));
        $this->assertTrue($mask->hasOneOf(Mask::FLAG_3, Mask::FLAG_13, Mask::FLAG_27));
        $this->assertTrue($mask->hasOneOf(Mask::FLAG_3, Mask::FLAG_17, Mask::FLAG_27));

        $this->assertFalse($mask->hasOneOf(Mask::FLAG_7, Mask::FLAG_17, Mask::FLAG_27));
    }

    public function testConstants()
    {
        $reflection = new \ReflectionClass(Mask::class);

        for ($i = 1; $i <= 32; ++$i) {
            $this->assertSame(2 ** ($i - 1), $reflection->getConstant('FLAG_'.$i));
        }
    }

    public function testFlagAddition()
    {
        $mask = new Mask();

        $mask->add(Mask::FLAG_9);
        $mask->add(Mask::FLAG_21);

        $this->assertTrue($mask->has(Mask::FLAG_9));
        $this->assertTrue($mask->has(Mask::FLAG_21));
    }

    public function testFlagRemoval()
    {
        $mask = new Mask(Mask::FLAG_9 | Mask::FLAG_21);

        $this->assertTrue($mask->has(Mask::FLAG_9));
        $this->assertTrue($mask->has(Mask::FLAG_21));

        $mask->remove(Mask::FLAG_9);
        $mask->remove(Mask::FLAG_21);

        $this->assertFalse($mask->has(Mask::FLAG_9));
        $this->assertFalse($mask->has(Mask::FLAG_21));
    }

    public function testAlreadyPresentFlagAddition()
    {
        $mask = new Mask(Mask::FLAG_13, false);

        // Should not throw an exception when not in strict mode.
        $mask->add(Mask::FLAG_13);

        $this->assertSame(Mask::FLAG_13, $mask->get());
    }

    public function testAlreadyPresentFlagAdditionInStrictMode()
    {
        $this->setExpectedException(\Mfonte\Bit\MaskException::class);

        $mask = new Mask(Mask::FLAG_13);

        // Should throw an exception in strict mode.
        $mask->add(Mask::FLAG_13);
    }

    public function testAbsentFlagRemoval()
    {
        $mask = new Mask(Mask::EMPTY_MASK, false);

        // Should not throw an exception when not in strict mode.
        $mask->remove(Mask::FLAG_13);

        $this->assertSame(Mask::EMPTY_MASK, $mask->get());
    }

    public function testAbsentFlagRemovalInStrictMode()
    {
        $this->setExpectedException(\Mfonte\Bit\MaskException::class);

        $mask = new Mask();

        // Should throw an exception in strict mode.
        $mask->remove(Mask::FLAG_13);
    }

    public function testGetFlags()
    {
        $mask = new Mask(Mask::FLAG_9 | Mask::FLAG_21);

        $this->assertSame(
            [
                Mask::FLAG_9,
                Mask::FLAG_21,
            ],
            $mask->getAll()
        );
    }

    public function testGetFlagsAfterAdd()
    {
        $mask = new Mask(Mask::FLAG_9 | Mask::FLAG_21);

        $mask->add(Mask::FLAG_19);

        $this->assertSame(
            [
                Mask::FLAG_9,
                Mask::FLAG_19,
                Mask::FLAG_21,
            ],
            $mask->getAll()
        );
    }

    public function testGetFlagsAfterRemove()
    {
        $mask = new Mask(Mask::FLAG_9 | Mask::FLAG_21);

        $mask->remove(Mask::FLAG_21);

        $this->assertSame(
            [
                Mask::FLAG_9,
            ],
            $mask->getAll()
        );
    }

    public function testMaskIteration()
    {
        $mask = new Mask(Mask::FLAG_7 | Mask::FLAG_13 | Mask::FLAG_17);

        $this->assertInstanceOf(\Traversable::class, $mask);

        foreach ($mask as $flag) {
            $this->assertTrue($flag === Mask::FLAG_7 || $flag === Mask::FLAG_13 || $flag === Mask::FLAG_17);
        }
    }
}
