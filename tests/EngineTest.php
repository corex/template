<?php

declare(strict_types=1);

namespace Tests\CoRex\Template;

use CoRex\Helpers\Obj;
use CoRex\Template\Helpers\Engine;
use CoRex\Template\Helpers\PathEntry;
use PHPUnit\Framework\TestCase;

class EngineTest extends TestCase
{
    /** @var string */
    private $randomString;

    /** @var int */
    private $randomNumber;

    /**
     * Test constructor default.
     *
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function testConstructorDefault(): void
    {
        $engine = new Engine();
        $this->assertNull(Obj::getProperty('templateName', $engine));
        $this->assertNull(Obj::getProperty('templateContent', $engine));
        $this->assertEquals([], Obj::getProperty('variables', $engine));
        $this->assertFalse(Obj::getProperty('escape', $engine));
        $this->assertEquals([], Obj::getProperty('pathEntries', $engine));
    }

    /**
     * Test constructor with template name.
     *
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function testConstructorWithTemplateName(): void
    {
        $engine = new Engine($this->randomString, null, [$this->randomString, $this->randomNumber]);
        $this->assertEquals($this->randomString, Obj::getProperty('templateName', $engine));
        $this->assertNull(Obj::getProperty('templateContent', $engine));
        $this->assertEquals([$this->randomString, $this->randomNumber], Obj::getProperty('pathEntries', $engine));
    }

    /**
     * Test constructor with template content.
     *
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function testConstructorWithTemplateContent(): void
    {
        $engine = new Engine(null, (string)$this->randomNumber, [$this->randomString, $this->randomNumber]);
        $this->assertNull(Obj::getProperty('templateName', $engine));
        $this->assertEquals($this->randomNumber, Obj::getProperty('templateContent', $engine));
        $this->assertEquals([$this->randomString, $this->randomNumber], Obj::getProperty('pathEntries', $engine));
    }

    /**
     * Test constructor exception.
     *
     * @throws \Exception
     */
    public function testConstructorException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('It is not allowed to set both name of template and content of template.');
        new Engine($this->randomString, (string)$this->randomNumber);
    }

    /**
     * Test escape.
     *
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function testEscape(): void
    {
        $engine = new Engine();
        $this->assertFalse(Obj::getProperty('escape', $engine));
        $engine->escape();
        $this->assertTrue(Obj::getProperty('escape', $engine));
    }

    /**
     * Test path.
     *
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function testPath(): void
    {
        $engine = new Engine();
        $this->assertEquals([], Obj::getProperty('pathEntries', $engine));
        $engine->path($this->randomString, (string)$this->randomNumber);
        $pathEntry = new PathEntry($this->randomString, (string)$this->randomNumber);
        $this->assertEquals([$pathEntry], Obj::getProperty('pathEntries', $engine));
    }

    /**
     * Test variable.
     *
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function testVariable(): void
    {
        $engine = new Engine();
        $this->assertEquals([], Obj::getProperty('variables', $engine));
        $engine->variable($this->randomString, $this->randomNumber);
        $this->assertEquals([$this->randomString => $this->randomNumber], Obj::getProperty('variables', $engine));
    }

    /**
     * Test variables.
     *
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function testVariables(): void
    {
        $checkArray = [$this->randomString => $this->randomNumber, (string)$this->randomNumber => $this->randomString];
        $engine = new Engine();
        $this->assertEquals([], Obj::getProperty('variables', $engine));
        $engine->variables($checkArray);
        $this->assertEquals($checkArray, Obj::getProperty('variables', $engine));
    }

    /**
     * Test render template name.
     *
     * @throws \Exception
     */
    public function testRenderTemplateName(): void
    {
        $pathEntry = new PathEntry(dirname(__DIR__) . '/templates', 'tpl');
        $engine = new Engine('test', null, [$pathEntry]);
        $engine->variable('test', $this->randomString);
        $result = $engine->render();
        $this->assertEquals('(' . $this->randomString . ')', $result);
    }

    /**
     * Test render template content.
     *
     * @throws \Exception
     */
    public function testRenderTemplateContent(): void
    {
        $template = '({{test}})';
        $engine = new Engine(null, $template);
        $engine->variable('test', $this->randomString);
        $check = str_replace('{{test}}', $this->randomString, $template);
        $this->assertEquals($check, $engine->render());
    }

    /**
     * Test render template exception.
     *
     * @throws \Exception
     */
    public function testRenderTemplateException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Neither template-name or template-content is set.');
        $engine = new Engine(null, null);
        $engine->render();
    }

    /**
     * Test render exception.
     *
     * @throws \Exception
     */
    public function testRenderException(): void
    {
        $engine = new Engine('test');
        $engine->variable('test', 'test');
        $result = (string)$engine;
        $this->assertSame('', $result);
    }

    /**
     * Test to string.
     *
     * @throws \Exception
     */
    public function testToString(): void
    {
        $template = '({{test}})';
        $engine = new Engine(null, $template);
        $engine->variable('test', $this->randomString);
        $check = str_replace('{{test}}', $this->randomString, $template);
        $this->assertEquals($check, (string)$engine);
    }

    /**
     * Test mustache engine.
     *
     * @throws \Exception
     */
    public function testMustacheEngine(): void
    {
        $engine = new Engine();
        $mustacheEngine = $engine->mustacheEngine();
        $this->assertInstanceOf(\Mustache_Engine::class, $mustacheEngine);
    }

    /**
     * Test not escaped.
     *
     * @throws \Exception
     */
    public function testNotEscaped(): void
    {
        $check = '<test>something</test>';
        $pathEntry = new PathEntry(dirname(__DIR__) . '/templates', 'tpl');
        $engine = new Engine('test', null, [$pathEntry]);
        $engine->variable('test', $check);
        $this->assertEquals('(' . $check . ')', $engine->render());
    }

    /**
     * Test escaped.
     *
     * @throws \Exception
     */
    public function testEscaped(): void
    {
        $check = '<test>something</test>';
        $pathEntry = new PathEntry(dirname(__DIR__) . '/templates', 'tpl');
        $engine = new Engine('test', null, [$pathEntry]);
        $engine->variable('test', $check);
        $engine->escape();
        $this->assertEquals(
            '(' . htmlspecialchars($check, ENT_COMPAT, 'UTF-8') . ')',
            $engine->render()
        );
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