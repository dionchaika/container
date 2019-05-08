<?php

/**
 * The PSR-11 DI Container.
 *
 * @package dionchaika/container
 * @version 1.0.0
 * @license MIT
 * @author Dion Chaika <dionchaika@gmail.com>
 */

namespace Dionchaika\Container;

class Collection
{
    /**
     * The array of entries.
     *
     * @var mixed[]
     */
    protected $entries = [];

    /**
     * @param mixed[] $entries
     */
    public function __construct(array $entries = [])
    {
        $this->entries = $entries;
    }

    /**
     * @return mixed[]
     */
    public function getAll(): array
    {
        return $this->entries;
    }

    /**
     * Check is the entry exists.
     *
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool
    {
        return isset($this->entries[$name]);
    }

    /**
     * Get the entry.
     *
     * @param string $name
     * @return mixed|null
     */
    public function get(string $name)
    {
        return $this->has($name) ? $this->entries[$name] : null;
    }

    /**
     * Set a new entry.
     *
     * @param string $name
     * @param mixed  $value
     * @return self
     */
    public function set(string $name, $value): self
    {
        $this->entries[$name] = $value;
        return $this;
    }

    /**
     * Delete an entry.
     *
     * @param string $name
     * @return self
     */
    public function delete(string $name): self
    {
        unset($this->entries[$name]);
        return $this;
    }
}
