# Git Fetch Command

The `api:git:fetch` artisan command allows you to fetch updates from a remote git repository.

## Syntax

```bash
php artisan api:git:fetch <path> [--remote=origin]
```

## Arguments

- `path` (required): The path to the git repository

## Options

- `--remote`: The remote name to fetch from (default: origin)

## Examples

### Basic usage with default remote

```bash
php artisan api:git:fetch /path/to/repo
```

### Fetch from a specific remote

```bash
php artisan api:git:fetch /path/to/repo --remote=upstream
```

## Validation

The command will validate:
- The specified path exists and is a directory
- The path contains a git repository (has a .git directory)

## Error Handling

The command will return appropriate error messages if:
- The path does not exist
- The path is not a git repository
- The git fetch operation fails

## Exit Codes

- `0`: Success
- `1`: Error (invalid path, not a git repo, or fetch failed)
