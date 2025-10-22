<?php

namespace Dingo\Api\Console\Command;

use Dingo\Api\Contract\Git\Service;
use Illuminate\Console\Command;

class GitFetch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    public $signature = 'api:git:fetch
                        {path : The repository path}
                        {--remote= : Remote to fetch from}
                        {--branch= : Branch to fetch}
                        {--prune : Remove remote-tracking references that no longer exist on remote}
                        {--all : Fetch all remotes}';

    /**
     * The console command description.
     *
     * @var string
     */
    public $description = 'Fetch from a git repository';

    /**
     * Git service instance.
     *
     * @var \Dingo\Api\Contract\Git\Service
     */
    protected $git;

    /**
     * Create a new git fetch command instance.
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

        $this->info("Fetching from repository: {$path}");

        $options = array_filter([
            'remote' => $this->option('remote'),
            'branch' => $this->option('branch'),
            'prune' => $this->option('prune'),
            'all' => $this->option('all'),
        ]);

        if (!empty($options)) {
            $this->line('Options: ' . json_encode($options));
        }

        $result = $this->git->fetch($path, $options);

        if ($result['success']) {
            $this->info('Fetch completed successfully!');

            if (!empty($result['output'])) {
                $this->line($result['output']);
            }

            if (!empty($result['error'])) {
                $this->line($result['error']);
            }

            return 0;
        } else {
            $this->error('Failed to fetch from repository.');

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
