<?php

declare(strict_types=1);

namespace Tests\CoRex\Template;

use CoRex\Helpers\Obj;
use CoRex\Template\Helpers\PathEntry;
use CoRex\Template\Template;
use PHPUnit\Framework\TestCase;

class TemplateTest extends TestCase
{
    /**
     * Test clear base paths.
     *
     * @throws \ReflectionException
     */
    public function testClearBasePaths(): void
    {
        $basePathEntries = Obj::getProperty('basePathEntries', null, [], Template::class);
        $this->assertEquals([], $basePathEntries);
        Template::basePath(dirname(__DIR__) . '/templates');
        $basePathEntries = Obj::getProperty('basePathEntries', null, null, Template::class);
        $this->assertGreaterThan(0, count($basePathEntries));
        Template::clearBasePaths();
        $basePathEntries = Obj::getProperty('basePathEntries', null, [], Template::class);
        $this->assertEquals([], $basePathEntries);
    }

    /**
     * Test clear paths.
     *
     * @throws \ReflectionException
     */
    public function testClearPaths(): void
    {
        $basePathEntries = Obj::getProperty('basePathEntries', null, [], Template::class);
        $this->assertEquals([], $basePathEntries);
        Template::path(dirname(__DIR__) . '/templates');
        $basePathEntries = Obj::getProperty('basePathEntries', null, null, Template::class);
        $this->assertGreaterThan(0, count($basePathEntries));
        Template::clearPaths();
        $basePathEntries = Obj::getProperty('basePathEntries', null, [], Template::class);
        $this->assertEquals([], $basePathEntries);
    }

    /**
     * Test base path.
     *
     * @throws \ReflectionException
     */
    public function testBasePath(): void
    {
        $basePathEntries = Obj::getProperty('basePathEntries', null, [], Template::class);
        $this->assertEquals([], $basePathEntries);

        Template::basePath(dirname(__DIR__) . '/templates');

        $basePathEntries = Obj::getProperty('basePathEntries', null, null, Template::class);

        $pathEntry = new PathEntry(dirname(__DIR__) . '/templates', 'tpl');
        $this->assertEquals([$pathEntry], $basePathEntries);
    }

    /**
     * Test path.
     *
     * @throws \ReflectionException
     */
    public function testPath(): void
    {
        $basePathEntries = Obj::getProperty('basePathEntries', null, [], Template::class);
        $this->assertEquals([], $basePathEntries);

        Template::path(dirname(__DIR__) . '/templates');

        $basePathEntries = Obj::getProperty('basePathEntries', null, null, Template::class);

        $pathEntry = new PathEntry(dirname(__DIR__) . '/templates', 'tpl');
        $this->assertEquals([$pathEntry], $basePathEntries);
    }

    /**
     * Test load found.
     *
     * @throws \Exception
     */
    public function testLoadFound(): void
    {
        Template::basePath(dirname(__DIR__) . '/templates');
        $this->assertEquals('()', Template::load('test')->render());
    }

    /**
     * Test load not found.
     *
     * @throws \Exception
     */
    public function testLoadNotFound(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unknown template: test');
        Template::load('test')->render();
    }

    /**
     * Test render.
     *
     * @throws \Exception
     */
    public function testRender(): void
    {
        Template::basePath(dirname(__DIR__) . '/templates');
        $check = md5((string)mt_rand(1, 100000));
        $content = Template::render('test', [
            'test' => $check
        ]);
        $this->assertEquals('(' . $check . ')', $content);
    }

    /**
     * Test parse.
     *
     * @throws \Exception
     */
    public function testParse(): void
    {
        $check = md5((string)mt_rand(1, 100000));
        $template = '({{test}})';
        $content = Template::parse($template, [
            'test' => $check
        ]);
        $checkContent = str_replace('{{test}}', $check, $template);
        $this->assertEquals($checkContent, $content);
    }

    /**
     * Test mustache engine.
     *
     * @throws \Exception
     */
    public function testMustacheEngine(): void
    {
        $mustacheEngine = Template::mustacheEngine();
        $this->assertInstanceOf(\Mustache_Engine::class, $mustacheEngine);
    }

    /**
     * Setup.
     */
    protected function setUp(): void
    {
        parent::setUp();
        Template::clearBasePaths();
    }
}