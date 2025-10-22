<?php

namespace Dingo\Api\Console\Command;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class GitFetch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    public $signature = 'api:git:fetch
                        {path : The path to the git repository}
                        {--remote=origin : The remote name to fetch from}';

    /**
     * The console command description.
     *
     * @var string
     */
    public $description = 'Fetch updates from a remote git repository';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $path = $this->argument('path');
        $remote = $this->option('remote');

        // Validate that the path exists
        if (!is_dir($path)) {
            $this->error("The path '{$path}' does not exist or is not a directory.");
            return 1;
        }

        // Validate that it's a git repository
        if (!is_dir($path . '/.git')) {
            $this->error("The path '{$path}' is not a git repository.");
            return 1;
        }

        $this->info("Fetching from remote '{$remote}' in repository: {$path}");

        try {
            // Create the process for git fetch
            $process = new Process(['git', 'fetch', $remote], $path);
            $process->setTimeout(300); // 5 minutes timeout
            $process->run();

            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            // Display the output
            $output = $process->getOutput();
            if (!empty($output)) {
                $this->line($output);
            }

            $errorOutput = $process->getErrorOutput();
            if (!empty($errorOutput)) {
                $this->comment($errorOutput);
            }

            $this->info("Successfully fetched from '{$remote}'.");
            return 0;
        } catch (ProcessFailedException $exception) {
            $this->error("Failed to fetch from remote '{$remote}':");
            $this->error($exception->getMessage());
            return 1;
        }
    }
}
