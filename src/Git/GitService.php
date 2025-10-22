<?php

namespace Dingo\Api\Git;

use Dingo\Api\Contract\Git\Service;
use RuntimeException;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class GitService implements Service
{
    /**
     * The default timeout for git operations in seconds.
     *
     * @var int
     */
    protected $timeout = 300;

    /**
     * Clone a git repository to a specified destination.
     *
     * @param string $repository
     * @param string $destination
     * @param array $options
     *
     * @return array
     */
    public function clone($repository, $destination, array $options = [])
    {
        $command = ['git', 'clone'];

        // Add common options
        if (isset($options['branch'])) {
            $command[] = '--branch';
            $command[] = $options['branch'];
        }

        if (isset($options['depth'])) {
            $command[] = '--depth';
            $command[] = $options['depth'];
        }

        if (isset($options['single-branch']) && $options['single-branch']) {
            $command[] = '--single-branch';
        }

        if (isset($options['recursive']) && $options['recursive']) {
            $command[] = '--recursive';
        }

        $command[] = $repository;
        $command[] = $destination;

        return $this->executeCommand($command, dirname($destination));
    }

    /**
     * Fetch from a git repository.
     *
     * @param string $path
     * @param array $options
     *
     * @return array
     */
    public function fetch($path, array $options = [])
    {
        if (!$this->isRepository($path)) {
            throw new RuntimeException("The path [{$path}] is not a git repository.");
        }

        $command = ['git', 'fetch'];

        if (isset($options['remote'])) {
            $command[] = $options['remote'];
        }

        if (isset($options['branch'])) {
            $command[] = $options['branch'];
        }

        if (isset($options['prune']) && $options['prune']) {
            $command[] = '--prune';
        }

        if (isset($options['all']) && $options['all']) {
            $command[] = '--all';
        }

        return $this->executeCommand($command, $path);
    }

    /**
     * Get the status of a git repository.
     *
     * @param string $path
     *
     * @return array
     */
    public function status($path)
    {
        if (!$this->isRepository($path)) {
            throw new RuntimeException("The path [{$path}] is not a git repository.");
        }

        return $this->executeCommand(['git', 'status', '--porcelain'], $path);
    }

    /**
     * Check if a directory is a git repository.
     *
     * @param string $path
     *
     * @return bool
     */
    public function isRepository($path)
    {
        if (!is_dir($path)) {
            return false;
        }

        $process = new Process(['git', 'rev-parse', '--git-dir'], $path);
        $process->run();

        return $process->isSuccessful();
    }

    /**
     * Execute a git command.
     *
     * @param array $command
     * @param string $workingDirectory
     *
     * @return array
     */
    protected function executeCommand(array $command, $workingDirectory = null)
    {
        $process = new Process($command, $workingDirectory, null, null, $this->timeout);

        try {
            $process->mustRun();

            return [
                'success' => true,
                'output' => $process->getOutput(),
                'error' => $process->getErrorOutput(),
                'exit_code' => $process->getExitCode(),
            ];
        } catch (ProcessFailedException $e) {
            return [
                'success' => false,
                'output' => $process->getOutput(),
                'error' => $process->getErrorOutput(),
                'exit_code' => $process->getExitCode(),
                'exception' => $e->getMessage(),
            ];
        }
    }

    /**
     * Set the timeout for git operations.
     *
     * @param int $timeout
     *
     * @return $this
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;

        return $this;
    }
}
