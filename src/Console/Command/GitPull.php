<?php

namespace Dingo\Api\Console\Command;

use Dingo\Api\Contract\Git\Service;
use Illuminate\Console\Command;

class GitPull extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    public $signature = 'api:git:pull
                        {path : The repository path}
                        {--remote= : Remote to pull from}
                        {--branch= : Branch to pull}
                        {--rebase : Rebase the current branch on top of the upstream branch}
                        {--no-commit : Perform the merge but do not commit}
                        {--ff-only : Only fast-forward merges}';

    /**
     * The console command description.
     *
     * @var string
     */
    public $description = 'Pull from a git repository';

    /**
     * Git service instance.
     *
     * @var \Dingo\Api\Contract\Git\Service
     */
    protected $git;

    /**
     * Create a new git pull command instance.
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

        if (!$this->git->isRepository($path)) {
            $this->error("The path [{$path}] is not a git repository.");
            return 1;
        }

        $this->info("Pulling from repository: {$path}");

        $options = array_filter([
            'remote' => $this->option('remote'),
            'branch' => $this->option('branch'),
            'rebase' => $this->option('rebase'),
            'no-commit' => $this->option('no-commit'),
            'ff-only' => $this->option('ff-only'),
        ]);

        if (!empty($options)) {
            $this->line('Options: ' . json_encode($options));
        }

        $result = $this->git->pull($path, $options);

        if ($result['success']) {
            $this->info('Pull completed successfully!');

            if (!empty($result['output'])) {
                $this->line($result['output']);
            }

            if (!empty($result['error'])) {
                $this->line($result['error']);
            }

            return 0;
        } else {
            $this->error('Failed to pull from repository.');

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
