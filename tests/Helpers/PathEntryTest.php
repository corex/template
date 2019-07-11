<?php

declare(strict_types=1);

namespace Tests\CoRex\Template\Helpers;

use CoRex\Helpers\Obj;
use CoRex\Template\Helpers\PathEntry;
use PHPUnit\Framework\TestCase;

class PathEntryTest extends TestCase
{
    /** @var string */
    private $randomString;

    /** @var int */
    private $randomNumber;

    /**
     * Test.
     *
     * @throws \ReflectionException
     */
    public function testConstructor(): void
    {
        $pathEntry = new PathEntry($this->randomString, (string)$this->randomNumber);
        $this->assertEquals($this->randomString, Obj::getProperty('path', $pathEntry));
        $this->assertEquals($this->randomNumber, Obj::getProperty('extension', $pathEntry));
    }

    /**
     * Setup.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->randomNumber = mt_rand(1, 100000);
        $this->randomString = md5((string)$this->randomNumber);
    }
}