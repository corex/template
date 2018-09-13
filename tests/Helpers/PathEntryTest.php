<?php

namespace Tests\CoRex\Template;

use CoRex\Helpers\Obj;
use CoRex\Template\Helpers\PathEntry;
use PHPUnit\Framework\TestCase;

class PathEntryTest extends TestCase
{
    private $randomString;
    private $randomNumber;

    /**
     * Test constructor.
     * @throws \ReflectionException
     */
    public function testConstructor()
    {
        $pathEntry = new PathEntry($this->randomString, $this->randomNumber);
        $this->assertEquals($this->randomString, Obj::getProperty('path', $pathEntry));
        $this->assertEquals($this->randomNumber, Obj::getProperty('extension', $pathEntry));
    }

    /**
     * Test get path.
     * @throws \ReflectionException
     */
    public function testGetPath()
    {
        $pathEntry = new PathEntry(null, null);
        Obj::setProperty('path', $pathEntry, $this->randomString);
        $this->assertEquals($this->randomString, $pathEntry->getPath());
        $this->assertNull($pathEntry->getExtension());
    }

    /**
     * Test get extension.
     * @throws \ReflectionException
     */
    public function testGetExtension()
    {
        $pathEntry = new PathEntry(null, null);
        Obj::setProperty('extension', $pathEntry, $this->randomString);
        $this->assertNull($pathEntry->getPath());
        $this->assertEquals($this->randomString, $pathEntry->getExtension());
    }

    /**
     * Setup.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->randomNumber = mt_rand(1, 100000);
        $this->randomString = md5($this->randomNumber);
    }
}