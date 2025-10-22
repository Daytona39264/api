<?php

namespace Dingo\Api\Console\Command;

use Dingo\Api\Contract\Git\Service;
use Illuminate\Console\Command;

class GitClone extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    public $signature = 'api:git:clone
                        {repository : The repository URL to clone}
                        {destination : The destination path}
                        {--branch= : Branch to clone}
                        {--depth= : Create a shallow clone with a history truncated to the specified number of commits}
                        {--single-branch : Clone only one branch}
                        {--recursive : Clone submodules recursively}';

    /**
     * The console command description.
     *
     * @var string
     */
    public $description = 'Clone a git repository';

    /**
     * Git service instance.
     *
     * @var \Dingo\Api\Contract\Git\Service
     */
    protected $git;

    /**
     * Create a new git clone command instance.
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
        $repository = $this->argument('repository');
        $destination = $this->argument('destination');

        $this->info("Cloning repository: {$repository}");
        $this->info("Destination: {$destination}");

        $options = array_filter([
            'branch' => $this->option('branch'),
            'depth' => $this->option('depth'),
            'single-branch' => $this->option('single-branch'),
            'recursive' => $this->option('recursive'),
        ]);

        if (!empty($options)) {
            $this->line('Options: ' . json_encode($options));
        }

        $result = $this->git->clone($repository, $destination, $options);

        if ($result['success']) {
            $this->info('Repository cloned successfully!');

            if (!empty($result['output'])) {
                $this->line($result['output']);
            }

            return 0;
        } else {
            $this->error('Failed to clone repository.');

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
