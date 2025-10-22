<?php

namespace Dingo\Api\Contract\Git;

interface Service
{
    /**
     * Clone a git repository to a specified destination.
     *
     * @param string $repository
     * @param string $destination
     * @param array $options
     *
     * @return array
     */
    public function clone($repository, $destination, array $options = []);

    /**
     * Fetch from a git repository.
     *
     * @param string $path
     * @param array $options
     *
     * @return array
     */
    public function fetch($path, array $options = []);

    /**
     * Get the status of a git repository.
     *
     * @param string $path
     *
     * @return array
     */
    public function status($path);

    /**
     * Check if a directory is a git repository.
     *
     * @param string $path
     *
     * @return bool
     */
    public function isRepository($path);
}
