<?php

namespace Dingo\Api\Console\Command;

use Dingo\Api\Contract\Git\Service;
use Illuminate\Console\Command;

class GitCommit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    public $signature = 'api:git:commit
                        {path : The repository path}
                        {message : Commit message}
                        {--all : Automatically stage all modified and deleted files}
                        {--amend : Amend the previous commit}
                        {--no-verify : Bypass pre-commit and commit-msg hooks}
                        {--author= : Override the commit author}';

    /**
     * The console command description.
     *
     * @var string
     */
    public $description = 'Commit changes in a git repository';

    /**
     * Git service instance.
     *
     * @var \Dingo\Api\Contract\Git\Service
     */
    protected $git;

    /**
     * Create a new git commit command instance.
     *
     * @param \Dingo\Api\Contract\Git\Service $git
     *
     * @return void
     */
    public function __construct(Service $git)
    {
        $this->git = $git;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $path = $this->argument('path');
        $message = $this->argument('message');

        if (!$this->git->isRepository($path)) {
            $this->error("The path [{$path}] is not a git repository.");
            return 1;
        }

        $this->info("Committing changes in repository: {$path}");
        $this->line("Message: {$message}");

        $options = array_filter([
            'all' => $this->option('all'),
            'amend' => $this->option('amend'),
            'no-verify' => $this->option('no-verify'),
            'author' => $this->option('author'),
        ]);

        if (!empty($options)) {
            $this->line('Options: ' . json_encode($options));
        }

        $result = $this->git->commit($path, $message, $options);

        if ($result['success']) {
            $this->info('Commit created successfully!');

            if (!empty($result['output'])) {
                $this->line($result['output']);
            }

            if (!empty($result['error'])) {
                $this->line($result['error']);
            }

            return 0;
        } else {
            $this->error('Failed to create commit.');

            if (!empty($result['error'])) {
                $this->error($result['error']);
            }

            if (isset($result['exception'])) {
                $this->error($result['exception']);
            }

            return 1;
        }
    }
}
