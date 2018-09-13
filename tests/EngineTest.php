<?php

namespace Tests\CoRex\Template;

use CoRex\Helpers\Obj;
use CoRex\Template\Helpers\Engine;
use CoRex\Template\Helpers\PathEntry;
use PHPUnit\Framework\TestCase;

class EngineTest extends TestCase
{
    private $randomString;
    private $randomNumber;

    /**
     * Test constructor default.
     *
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function testConstructorDefault()
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
    public function testConstructorWithTemplateName()
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
    public function testConstructorWithTemplateContent()
    {
        $engine = new Engine(null, $this->randomNumber, [$this->randomString, $this->randomNumber]);
        $this->assertNull(Obj::getProperty('templateName', $engine));
        $this->assertEquals($this->randomNumber, Obj::getProperty('templateContent', $engine));
        $this->assertEquals([$this->randomString, $this->randomNumber], Obj::getProperty('pathEntries', $engine));
    }

    /**
     * Test constructor exception.
     *
     * @throws \Exception
     */
    public function testConstructorException()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('It is not allowed to set both name of template and content of template.');
        new Engine($this->randomString, $this->randomNumber);
    }

    /**
     * Test escape.
     *
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function testEscape()
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
    public function testPath()
    {
        $engine = new Engine();
        $this->assertEquals([], Obj::getProperty('pathEntries', $engine));
        $engine->path($this->randomString, $this->randomNumber);
        $pathEntry = new PathEntry($this->randomString, $this->randomNumber);
        $this->assertEquals([$pathEntry], Obj::getProperty('pathEntries', $engine));
    }

    /**
     * Test variable.
     *
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function testVariable()
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
    public function testVariables()
    {
        $checkArray = [$this->randomString => $this->randomNumber, $this->randomNumber => $this->randomString];
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
    public function testRenderTemplateName()
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
    public function testRenderTemplateContent()
    {
        $template = '({{test}})';
        $engine = new Engine(null, $template);
        $engine->variable('test', $this->randomString);
        $check = str_replace('{{test}}', $this->randomString, $template);
        $this->assertEquals($check, $engine->render());
    }

    /**
     * Test render exception.
     *
     * @throws \Exception
     */
    public function testRenderException()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Neither template-name or template-content is set.');
        $engine = new Engine(null, null);
        $engine->render();
    }

    /**
     * Test to string.
     *
     * @throws \Exception
     */
    public function testToString()
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
    public function testMustacheEngine()
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
    public function testNotEscaped()
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
    public function testEscaped()
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
    protected function setUp()
    {
        parent::setUp();
        $this->randomNumber = mt_rand(1, 100000);
        $this->randomString = md5($this->randomNumber);
    }
}