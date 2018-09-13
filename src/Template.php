<?php

namespace CoRex\Template;

use CoRex\Template\Helpers\Engine;
use CoRex\Template\Helpers\PathEntry;

class Template
{
    /**
     * @var PathEntry[]
     */
    private static $basePathEntries;

    /**
     * Clear base paths.
     */
    public static function clearBasePaths()
    {
        self::initialize();
        self::$basePathEntries = [];
    }

    /**
     * Set base path (can be called more than once).
     * Note: paths will be searched in reverse order.
     *
     * @param string $path
     * @param string $extension
     */
    public static function basePath($path, $extension = 'tpl')
    {
        self::initialize();
        self::$basePathEntries[] = new PathEntry($path, $extension);
    }

    /**
     * Load.
     *
     * @param string $templateName
     * @return Engine
     * @throws \Exception
     */
    public static function load($templateName)
    {
        self::initialize();
        $engine = new Engine($templateName, null, self::$basePathEntries);
        return $engine;
    }

    /**
     * Make.
     *
     * @param string $templateName
     * @param array $variables
     * @return string
     * @throws \Exception
     */
    public static function render($templateName, array $variables = [])
    {
        $engine = self::load($templateName);
        $engine->variables($variables);
        return $engine->render();
    }

    /**
     * Parse.
     *
     * @param string $content
     * @param array $variables
     * @return string
     * @throws \Exception
     */
    public static function parse($content, array $variables = [])
    {
        self::initialize();
        $engine = new Engine(null, $content);
        $engine->variables($variables);
        return $engine->render();
    }

    /**
     * Mustache engine.
     *
     * @param boolean $addBasePaths Default true.
     * @return \Mustache_Engine
     * @throws \Exception
     */
    public static function mustacheEngine($addBasePaths = true)
    {
        self::initialize();
        $engine = new Engine();
        return $engine->mustacheEngine($addBasePaths);
    }

    /**
     * Initialize.
     */
    private static function initialize()
    {
        if (!is_array(self::$basePathEntries)) {
            self::$basePathEntries = [];
        }
    }
}