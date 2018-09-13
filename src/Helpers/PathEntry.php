<?php

namespace CoRex\Template\Helpers;

class PathEntry
{
    private $path;
    private $extension;

    /**
     * PathEntry constructor.
     * @param string $path
     * @param string $extension
     */
    public function __construct($path, $extension)
    {
        $this->path = $path;
        $this->extension = $extension;
    }

    /**
     * Get path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Get extension.
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }
}