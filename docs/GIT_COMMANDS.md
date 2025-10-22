# Git Commands

Dingo API now includes built-in git operations support through console commands and a service layer.

## Table of Contents

- [Installation](#installation)
- [Console Commands](#console-commands)
  - [Clone Command](#clone-command)
  - [Fetch Command](#fetch-command)
  - [Pull Command](#pull-command)
  - [Commit Command](#commit-command)
  - [Push Command](#push-command)
- [Using the Git Service](#using-the-git-service)
- [API Reference](#api-reference)

## Installation

The git functionality is automatically available when you install Dingo API. Make sure git is installed on your system:

```bash
git --version
```

## Console Commands

### Clone Command

Clone a git repository to a specified destination.

**Signature:**
```bash
php artisan api:git:clone {repository} {destination} [options]
```

**Arguments:**
- `repository` - The repository URL to clone (HTTP, HTTPS, or SSH)
- `destination` - The destination path where the repository will be cloned

**Options:**
- `--branch=BRANCH` - Specific branch to clone
- `--depth=NUMBER` - Create a shallow clone with history truncated to specified number of commits
- `--single-branch` - Clone only one branch (the HEAD or specified with --branch)
- `--recursive` - Clone submodules recursively

**Examples:**

Clone a repository:
```bash
php artisan api:git:clone https://github.com/user/repo.git /var/www/repo
```

Clone a specific branch:
```bash
php artisan api:git:clone https://github.com/user/repo.git /var/www/repo --branch=develop
```

Clone with shallow depth (faster for large repositories):
```bash
php artisan api:git:clone https://github.com/user/repo.git /var/www/repo --depth=1
```

Clone a single branch only:
```bash
php artisan api:git:clone https://github.com/user/repo.git /var/www/repo --branch=main --single-branch
```

Clone with submodules:
```bash
php artisan api:git:clone https://github.com/user/repo.git /var/www/repo --recursive
```

### Fetch Command

Fetch updates from a git repository.

**Signature:**
```bash
php artisan api:git:fetch {path} [options]
```

**Arguments:**
- `path` - The path to the git repository

**Options:**
- `--remote=REMOTE` - Specific remote to fetch from (default: origin)
- `--branch=BRANCH` - Specific branch to fetch
- `--prune` - Remove remote-tracking references that no longer exist on remote
- `--all` - Fetch all remotes

**Examples:**

Fetch from origin:
```bash
php artisan api:git:fetch /var/www/repo
```

Fetch from a specific remote:
```bash
php artisan api:git:fetch /var/www/repo --remote=upstream
```

Fetch a specific branch:
```bash
php artisan api:git:fetch /var/www/repo --branch=main
```

Fetch and prune deleted branches:
```bash
php artisan api:git:fetch /var/www/repo --prune
```

Fetch from all remotes:
```bash
php artisan api:git:fetch /var/www/repo --all
```

### Pull Command

Pull (fetch and merge) updates from a git repository.

**Signature:**
```bash
php artisan api:git:pull {path} [options]
```

**Arguments:**
- `path` - The path to the git repository

**Options:**
- `--remote=REMOTE` - Remote to pull from (default: origin)
- `--branch=BRANCH` - Branch to pull
- `--rebase` - Rebase the current branch on top of the upstream branch
- `--no-commit` - Perform the merge but do not commit
- `--ff-only` - Only allow fast-forward merges

**Examples:**

Pull from origin:
```bash
php artisan api:git:pull /var/www/repo
```

Pull from a specific remote and branch:
```bash
php artisan api:git:pull /var/www/repo --remote=upstream --branch=main
```

Pull with rebase:
```bash
php artisan api:git:pull /var/www/repo --rebase
```

Pull with fast-forward only:
```bash
php artisan api:git:pull /var/www/repo --ff-only
```

### Commit Command

Commit changes in a git repository.

**Signature:**
```bash
php artisan api:git:commit {path} {message} [options]
```

**Arguments:**
- `path` - The path to the git repository
- `message` - Commit message

**Options:**
- `--all` - Automatically stage all modified and deleted files
- `--amend` - Amend the previous commit
- `--no-verify` - Bypass pre-commit and commit-msg hooks
- `--author=AUTHOR` - Override the commit author

**Examples:**

Commit staged changes:
```bash
php artisan api:git:commit /var/www/repo "Fix bug in authentication"
```

Commit all changes (staged and unstaged):
```bash
php artisan api:git:commit /var/www/repo "Update dependencies" --all
```

Amend the previous commit:
```bash
php artisan api:git:commit /var/www/repo "Updated message" --amend
```

Commit with custom author:
```bash
php artisan api:git:commit /var/www/repo "Deploy to production" --author="Deploy Bot <bot@example.com>"
```

### Push Command

Push commits to a remote git repository.

**Signature:**
```bash
php artisan api:git:push {path} [options]
```

**Arguments:**
- `path` - The path to the git repository

**Options:**
- `--remote=REMOTE` - Remote to push to (default: origin)
- `--branch=BRANCH` - Branch to push
- `--force` - Force push (use with caution!)
- `--set-upstream` - Set upstream tracking for the branch
- `--tags` - Push all tags
- `--all` - Push all branches

**Examples:**

Push to origin:
```bash
php artisan api:git:push /var/www/repo
```

Push to a specific remote and branch:
```bash
php artisan api:git:push /var/www/repo --remote=origin --branch=main
```

Push and set upstream:
```bash
php artisan api:git:push /var/www/repo --set-upstream --remote=origin --branch=feature-branch
```

Push all tags:
```bash
php artisan api:git:push /var/www/repo --tags
```

## Using the Git Service

You can also use the Git service directly in your application code.

### Dependency Injection

```php
use Dingo\Api\Contract\Git\Service as GitService;

class DeploymentController extends Controller
{
    protected $git;

    public function __construct(GitService $git)
    {
        $this->git = $git;
    }

    public function deploy()
    {
        // Clone a repository
        $result = $this->git->clone(
            'https://github.com/user/repo.git',
            '/var/www/repo',
            ['branch' => 'main', 'depth' => 1]
        );

        if ($result['success']) {
            return response()->json(['message' => 'Repository cloned successfully']);
        }

        return response()->json(['error' => $result['error']], 500);
    }
}
```

### Service Resolution

```php
$git = app(\Dingo\Api\Contract\Git\Service::class);

// Clone a repository
$result = $git->clone(
    'https://github.com/user/repo.git',
    '/var/www/repo'
);

// Fetch updates
$result = $git->fetch('/var/www/repo', ['prune' => true]);

// Check repository status
$result = $git->status('/var/www/repo');

// Verify if path is a git repository
$isRepo = $git->isRepository('/var/www/repo');
```

## API Reference

### GitService Methods

#### clone($repository, $destination, array $options = [])

Clone a git repository.

**Parameters:**
- `string $repository` - Repository URL
- `string $destination` - Destination path
- `array $options` - Optional parameters:
  - `branch` (string) - Branch to clone
  - `depth` (int) - Shallow clone depth
  - `single-branch` (bool) - Clone single branch only
  - `recursive` (bool) - Clone submodules recursively

**Returns:**
```php
[
    'success' => true|false,
    'output' => string,
    'error' => string,
    'exit_code' => int,
    'exception' => string  // Only present on failure
]
```

#### fetch($path, array $options = [])

Fetch from a git repository.

**Parameters:**
- `string $path` - Repository path
- `array $options` - Optional parameters:
  - `remote` (string) - Remote to fetch from
  - `branch` (string) - Branch to fetch
  - `prune` (bool) - Prune deleted references
  - `all` (bool) - Fetch all remotes

**Returns:**
```php
[
    'success' => true|false,
    'output' => string,
    'error' => string,
    'exit_code' => int,
    'exception' => string  // Only present on failure
]
```

**Throws:**
- `RuntimeException` - If path is not a git repository

#### pull($path, array $options = [])

Pull (fetch and merge) from a git repository.

**Parameters:**
- `string $path` - Repository path
- `array $options` - Optional parameters:
  - `remote` (string) - Remote to pull from
  - `branch` (string) - Branch to pull
  - `rebase` (bool) - Rebase instead of merge
  - `no-commit` (bool) - Perform merge but do not commit
  - `ff-only` (bool) - Only allow fast-forward merges

**Returns:**
```php
[
    'success' => true|false,
    'output' => string,
    'error' => string,
    'exit_code' => int,
    'exception' => string  // Only present on failure
]
```

**Throws:**
- `RuntimeException` - If path is not a git repository

#### commit($path, $message, array $options = [])

Commit changes in a git repository.

**Parameters:**
- `string $path` - Repository path
- `string $message` - Commit message
- `array $options` - Optional parameters:
  - `all` (bool) - Stage all modified and deleted files
  - `amend` (bool) - Amend the previous commit
  - `no-verify` (bool) - Bypass hooks
  - `author` (string) - Override commit author

**Returns:**
```php
[
    'success' => true|false,
    'output' => string,
    'error' => string,
    'exit_code' => int,
    'exception' => string  // Only present on failure
]
```

**Throws:**
- `RuntimeException` - If path is not a git repository

#### push($path, array $options = [])

Push to a remote git repository.

**Parameters:**
- `string $path` - Repository path
- `array $options` - Optional parameters:
  - `remote` (string) - Remote to push to
  - `branch` (string) - Branch to push
  - `force` (bool) - Force push
  - `set-upstream` (bool) - Set upstream tracking
  - `tags` (bool) - Push all tags
  - `all` (bool) - Push all branches

**Returns:**
```php
[
    'success' => true|false,
    'output' => string,
    'error' => string,
    'exit_code' => int,
    'exception' => string  // Only present on failure
]
```

**Throws:**
- `RuntimeException` - If path is not a git repository

#### status($path)

Get the status of a git repository.

**Parameters:**
- `string $path` - Repository path

**Returns:**
```php
[
    'success' => true|false,
    'output' => string,  // Porcelain format output
    'error' => string,
    'exit_code' => int
]
```

**Throws:**
- `RuntimeException` - If path is not a git repository

#### isRepository($path)

Check if a directory is a git repository.

**Parameters:**
- `string $path` - Directory path

**Returns:**
- `bool` - True if the path is a git repository

#### setTimeout($timeout)

Set the timeout for git operations (in seconds).

**Parameters:**
- `int $timeout` - Timeout in seconds

**Returns:**
- `$this` - For method chaining

**Example:**
```php
$git->setTimeout(600)->clone($repo, $dest);
```

## Error Handling

All git operations return an array with detailed information. Always check the `success` key:

```php
$result = $git->clone($repo, $dest);

if (!$result['success']) {
    // Handle error
    Log::error('Git clone failed', [
        'error' => $result['error'],
        'exit_code' => $result['exit_code'],
        'exception' => $result['exception'] ?? null
    ]);
}
```

For `fetch()` and `status()`, a `RuntimeException` is thrown if the path is not a git repository:

```php
try {
    $result = $git->fetch('/invalid/path');
} catch (\RuntimeException $e) {
    Log::error('Not a git repository: ' . $e->getMessage());
}
```

## Timeout Configuration

The default timeout for git operations is 300 seconds (5 minutes). For large repositories, you may need to increase this:

```php
$git->setTimeout(600)->clone($largeRepo, $dest);
```

## Security Considerations

- Always validate and sanitize repository URLs from user input
- Be cautious with file paths to prevent directory traversal attacks
- Consider using SSH keys for authentication instead of embedding credentials in URLs
- Limit the directories where repositories can be cloned
- Use appropriate file permissions for cloned repositories

## Examples

### Automated Deployment

```php
public function deployLatest(GitService $git)
{
    $repoPath = '/var/www/myapp';

    if (!$git->isRepository($repoPath)) {
        // First deployment - clone
        $result = $git->clone(
            'git@github.com:company/app.git',
            $repoPath,
            ['branch' => 'production']
        );
    } else {
        // Subsequent deployments - fetch
        $result = $git->fetch($repoPath, [
            'remote' => 'origin',
            'branch' => 'production',
            'prune' => true
        ]);
    }

    if (!$result['success']) {
        throw new DeploymentException($result['error']);
    }
}
```

### Repository Mirror

```php
public function mirrorRepository(GitService $git, $source, $destination)
{
    // Clone with all branches
    $result = $git->clone($source, $destination);

    if ($result['success']) {
        // Fetch from all remotes
        $git->fetch($destination, ['all' => true]);
    }

    return $result;
}
```

## Troubleshooting

### Command Not Found

If you get "command not found" errors, ensure git is installed and in the system PATH.

### Permission Denied

Ensure the web server user has appropriate permissions to write to the destination directory and read git configuration.

### Timeout Errors

For large repositories, increase the timeout:
```php
$git->setTimeout(1800); // 30 minutes
```

### Authentication Issues

For private repositories, ensure proper authentication is configured:
- SSH: Set up SSH keys for the web server user
- HTTPS: Use credential helpers or embed credentials (not recommended for production)
