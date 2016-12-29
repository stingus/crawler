<?php

namespace Stingus\Crawler\Storage;

/**
 * Interface StorageInterface
 *
 * @package Stingus\Crawler\Storage
 */
interface StorageInterface
{
    /**
     * Saves the results to storage
     *
     * @param \ArrayObject $results
     * @return bool
     */
    public function save(\ArrayObject $results);
}
