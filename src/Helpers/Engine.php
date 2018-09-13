<?php

namespace CoRex\Template\Helpers;

class Engine
{
    private $templateName;
    private $templateContent;
    private $variables;
    private $escape;

    /**
     * @var PathEntry[]
     */
    private $pathEntries;

    /**
     * Engine constructor.
     *
     * @param string $templateName
     * @param string $templateContent
     * @param array $basePathEntries
     * @throws \Exception
     */
    public function __construct($templateName = null, $templateContent = null, array $basePathEntries = [])
    {
        $this->templateName = $templateName;
        $this->templateContent = $templateContent;
        $this->variables = [];
        $this->escape = false;

        //
        if ($templateName !== null && $templateContent !== null) {
            throw new \Exception('It is not allowed to set both name of template and content of template.');
        }

        // Add base path entries.
        $this->pathEntries = [];
        if (count($basePathEntries) > 0) {
            foreach ($basePathEntries as $basePathEntry) {
                $this->pathEntries[] = $basePathEntry;
            }
        }
    }

    /**
     * Escape variables.
     *
     * @param boolean $escape
     * @return $this
     */
    public function escape($escape = true)
    {
        $this->escape = $escape;
        return $this;
    }

    /**
     * Path (can be called multiple times).
     *
     * @param string $path
     * @param string $extension Default 'tpl'.
     * @return $this
     */
    public function path($path, $extension = 'tpl')
    {
        $this->pathEntries[] = new PathEntry($path, $extension);
        return $this;
    }

    /**
     * Variable.
     *
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function variable($name, $value)
    {
        $this->variables[$name] = $value;
        return $this;
    }

    /**
     * Context.
     *
     * @param array $variables
     * @return $this
     */
    public function variables(array $variables)
    {
        if (count($variables) > 0) {
            foreach ($variables as $name => $value) {
                $this->variable($name, $value);
            }
        }
        return $this;
    }

    /**
     * Render.
     *
     * @return string
     * @throws \Exception
     */
    public function render()
    {
        try {
            if ($this->templateContent !== null) {
                $result = $this->renderTemplateContent();
            } elseif ($this->templateName !== null) {
                $result = $this->renderTemplateName();
            } else {
                throw new \Exception('Neither template-name or template-content is set.');
            }
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(), $exception->getCode());
        }

        return $result;
    }

    /**
     * To string.
     *
     * @return string
     * @throws \Exception
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * Mustache engone.
     *
     * @param boolean $addBasePaths
     * @return \Mustache_Engine
     */
    public function mustacheEngine($addBasePaths = true)
    {
        $mustacheEngine = $this->mustacheEngineInitialize();
        if ($addBasePaths) {
            $this->mustacheAddFilesystemLoaders($mustacheEngine);
        }
        return $mustacheEngine;
    }

    /**
     * Render template content.
     *
     * @return string
     */
    private function renderTemplateContent()
    {
        $mustacheEngine = $this->mustacheEngineInitialize();
        return $mustacheEngine->render($this->templateContent, $this->variables);
    }

    /**
     * Render template name.
     *
     * @return string
     */
    private function renderTemplateName()
    {
        $mustacheEngine = $this->mustacheEngineInitialize();
        $this->mustacheAddFilesystemLoaders($mustacheEngine);
        return $mustacheEngine->render($this->templateName, $this->variables);
    }

    /**
     * Mustache add file system loaders.
     *
     * @param \Mustache_Engine $engine
     */
    private function mustacheAddFilesystemLoaders(\Mustache_Engine &$engine)
    {
        // Get and reverse path entries to have the newest first.
        $pathEntries = $this->pathEntries;
        rsort($pathEntries);

        // Add filesystem loaders.
        $cascadingLoader = new \Mustache_Loader_CascadingLoader();
        foreach ($pathEntries as $pathEntry) {
            $filesystemLoader = new \Mustache_Loader_FilesystemLoader($pathEntry->getPath(), [
                'extension' => $pathEntry->getExtension()
            ]);
            $cascadingLoader->addLoader($filesystemLoader);
        }
        $engine->setLoader($cascadingLoader);
    }

    /**
     * Mustache engine.
     *
     * @return \Mustache_Engine
     */
    private function mustacheEngineInitialize()
    {
        $options = [];

        // Set escape function.
        if ($this->escape) {
            $options['escape'] = function ($value) {
                return htmlspecialchars($value, ENT_COMPAT, 'UTF-8');
            };
        } else {
            $options['escape'] = function ($value) {
                return $value;
            };
        }

        $engine = new \Mustache_Engine($options);
        return $engine;
    }
}