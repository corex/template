<?php

declare(strict_types=1);

namespace CoRex\Template\Helpers;

class PathEntry
{
    /** @var string */
    private $path;

    /** @var string */
    private $extension;

    /**
     * PathEntry.
     *
     * @param string $path
     * @param string $extension
     */
    public function __construct(string $path, string $extension)
    {
        $this->path = $path;
        $this->extension = $extension;
    }

    /**
     * Get path.
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Get extension.
     *
     * @return string
     */
    public function getExtension(): string
    {
        return $this->extension;
    }
}