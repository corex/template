<?php

declare(strict_types=1);

namespace CoRex\Template\Helpers;

use CoRex\Template\Exceptions\TemplateException;
use Mustache_Engine;
use Mustache_Loader_CascadingLoader;
use Mustache_Loader_FilesystemLoader;

class Engine
{
    /** @var string|null */
    private $templateName;

    /** @var string|null */
    private $templateContent;

    /** @var string[] */
    private $variables;

    /** @var bool */
    private $escape;

    /** @var PathEntry[] */
    private $pathEntries;

    /**
     * Engine.
     *
     * @param string $templateName
     * @param string $templateContent
     * @param string[] $basePathEntries
     * @throws TemplateException
     */
    public function __construct(
        ?string $templateName = null,
        ?string $templateContent = null,
        array $basePathEntries = []
    ) {
        $this->templateName = $templateName;
        $this->templateContent = $templateContent;
        $this->variables = [];
        $this->escape = false;

        if ($templateName !== null && $templateContent !== null) {
            throw new TemplateException('It is not allowed to set both name of template and content of template.');
        }

        // Add base path entries.
        $this->pathEntries = [];
        if (count($basePathEntries) === 0) {
            return;
        }
        foreach ($basePathEntries as $basePathEntry) {
            $this->pathEntries[] = $basePathEntry;
        }
    }

    /**
     * Escape variables.
     *
     * @param bool $escape
     * @return $this
     */
    public function escape(bool $escape = true)
    {
        $this->escape = $escape;
        return $this;
    }

    /**
     * Path (can be called multiple times).
     *
     * @param string $path
     * @param string $extension Default 'tpl'.
     * @return Engine
     */
    public function path(string $path, string $extension = 'tpl'): self
    {
        $this->pathEntries[] = new PathEntry($path, $extension);
        return $this;
    }

    /**
     * Variable.
     *
     * @param string $name
     * @param mixed $value
     * @return Engine
     */
    public function variable(string $name, $value): self
    {
        $this->variables[$name] = $value;
        return $this;
    }

    /**
     * Var (shortcut to variable).
     *
     * @param string $name
     * @param mixed $value
     * @return Engine
     */
    public function var(string $name, $value): self
    {
        $this->variable($name, $value);
        return $this;
    }

    /**
     * Variables.
     *
     * @param string[] $variables
     * @return Engine
     */
    public function variables(array $variables): self
    {
        if (count($variables) > 0) {
            foreach ($variables as $name => $value) {
                $this->variable((string)$name, $value);
            }
        }
        return $this;
    }

    /**
     * Vars (shortcut to variables).
     *
     * @param string[] $variables
     * @return Engine
     */
    public function vars(array $variables): self
    {
        $this->variables($variables);
        return $this;
    }

    /**
     * Render.
     *
     * @throws TemplateException
     */
    public function render(): string
    {
        if ($this->templateContent !== null) {
            $result = $this->renderTemplateContent();
        } elseif ($this->templateName !== null) {
            $result = $this->renderTemplateName();
        } else {
            throw new TemplateException('Neither template-name or template-content is set.');
        }
        return $result;
    }

    /**
     * To string.
     *
     * @throws \Exception
     */
    public function __toString(): string
    {
        try {
            return $this->render();
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return '';
        }
    }

    /**
     * Mustache engine.
     *
     * @param bool $addBasePaths
     * @return Mustache_Engine
     */
    public function mustacheEngine(bool $addBasePaths = true): Mustache_Engine
    {
        $mustacheEngine = $this->mustacheEngineInitialize();
        if ($addBasePaths) {
            $this->mustacheAddFilesystemLoaders($mustacheEngine);
        }
        return $mustacheEngine;
    }

    /**
     * Render template content.
     */
    private function renderTemplateContent(): string
    {
        $mustacheEngine = $this->mustacheEngineInitialize();
        return $mustacheEngine->render($this->templateContent, $this->variables);
    }

    /**
     * Render template name.
     */
    private function renderTemplateName(): string
    {
        $mustacheEngine = $this->mustacheEngineInitialize();
        $this->mustacheAddFilesystemLoaders($mustacheEngine);
        return $mustacheEngine->render($this->templateName, $this->variables);
    }

    /**
     * Mustache add file system loaders.
     *
     * @param Mustache_Engine $engine
     */
    private function mustacheAddFilesystemLoaders(Mustache_Engine &$engine): void
    {
        // Get and reverse path entries to have the newest first.
        $pathEntries = $this->pathEntries;
        rsort($pathEntries);

        // Add filesystem loaders.
        $cascadingLoader = new Mustache_Loader_CascadingLoader();
        foreach ($pathEntries as $pathEntry) {
            $filesystemLoader = new Mustache_Loader_FilesystemLoader($pathEntry->getPath(), [
                'extension' => $pathEntry->getExtension()
            ]);
            $cascadingLoader->addLoader($filesystemLoader);
        }
        $engine->setLoader($cascadingLoader);
    }

    /**
     * Mustache engine.
     */
    private function mustacheEngineInitialize(): Mustache_Engine
    {
        $options = [];

        // Set escape function.
        if ($this->escape) {
            $options['escape'] = static function ($value) {
                return htmlspecialchars($value, ENT_COMPAT, 'UTF-8');
            };
        } else {
            $options['escape'] = static function ($value) {
                return $value;
            };
        }

        return new Mustache_Engine($options);
    }
}