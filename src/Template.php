<?php

declare(strict_types=1);

namespace CoRex\Template;

use CoRex\Template\Helpers\Engine;
use CoRex\Template\Helpers\PathEntry;

class Template
{
    /** @var PathEntry[] */
    private static $basePathEntries;

    /**
     * Clear base paths.
     */
    public static function clearBasePaths(): void
    {
        self::initialize();
        self::$basePathEntries = [];
    }

    /**
     * Clear base paths (shortcut for clearBasePaths).
     */
    public static function clearPaths(): void
    {
        self::clearBasePaths();
    }

    /**
     * Set base path (can be called more than once).
     * Note: paths will be searched in reverse order.
     *
     * @param string $path
     * @param string $extension
     */
    public static function basePath(string $path, string $extension = 'tpl'): void
    {
        self::initialize();
        self::$basePathEntries[] = new PathEntry($path, $extension);
    }

    /**
     * Set path (can be called more than once).
     * (shortcut for basePath).
     * Note: paths will be searched in reverse order.
     *
     * @param string $path
     * @param string $extension
     */
    public static function path(string $path, string $extension = 'tpl'): void
    {
        self::basePath($path, $extension);
    }

    /**
     * Load.
     *
     * @param string $templateName
     * @return Engine
     * @throws Exceptions\TemplateException
     */
    public static function load(string $templateName): Engine
    {
        self::initialize();
        return new Engine($templateName, null, self::$basePathEntries);
    }

    /**
     * Make.
     *
     * @param string $templateName
     * @param string[] $variables
     * @return string
     * @throws Exceptions\TemplateException
     */
    public static function render(string $templateName, array $variables = []): string
    {
        $engine = self::load($templateName);
        $engine->variables($variables);
        return $engine->render();
    }

    /**
     * Parse.
     *
     * @param string $content
     * @param string[] $variables
     * @return string
     * @throws Exceptions\TemplateException
     */
    public static function parse(string $content, array $variables = []): string
    {
        self::initialize();
        $engine = new Engine(null, $content);
        $engine->variables($variables);
        return $engine->render();
    }

    /**
     * Mustache engine.
     *
     * @param bool $addBasePaths Default true.
     * @return \Mustache_Engine
     * @throws Exceptions\TemplateException
     */
    public static function mustacheEngine(bool $addBasePaths = true): \Mustache_Engine
    {
        self::initialize();
        $engine = new Engine();
        return $engine->mustacheEngine($addBasePaths);
    }

    /**
     * Initialize.
     */
    private static function initialize(): void
    {
        if (!is_array(self::$basePathEntries)) {
            self::$basePathEntries = [];
        }
    }
}