<?php

namespace Dingo\Api\Tests\Git;

use Dingo\Api\Git\GitService;
use Dingo\Api\Tests\BaseTestCase;
use RuntimeException;

class GitServiceTest extends BaseTestCase
{
    /**
     * @var GitService
     */
    protected $gitService;

    public function setUp(): void
    {
        parent::setUp();
        $this->gitService = new GitService();
    }

    public function testIsRepositoryReturnsFalseForNonExistentPath()
    {
        $result = $this->gitService->isRepository('/path/that/does/not/exist');
        $this->assertFalse($result);
    }

    public function testIsRepositoryReturnsFalseForNonGitDirectory()
    {
        $tempDir = sys_get_temp_dir() . '/test_non_git_' . uniqid();
        mkdir($tempDir);

        $result = $this->gitService->isRepository($tempDir);
        $this->assertFalse($result);

        rmdir($tempDir);
    }

    public function testIsRepositoryReturnsTrueForGitRepository()
    {
        // Use the current API directory which is a git repository
        $result = $this->gitService->isRepository(__DIR__ . '/../..');
        $this->assertTrue($result);
    }

    public function testFetchThrowsExceptionForNonRepository()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('is not a git repository');

        $this->gitService->fetch('/path/that/does/not/exist');
    }

    public function testStatusThrowsExceptionForNonRepository()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('is not a git repository');

        $this->gitService->status('/path/that/does/not/exist');
    }

    public function testSetTimeoutReturnsInstance()
    {
        $result = $this->gitService->setTimeout(600);
        $this->assertInstanceOf(GitService::class, $result);
    }

    public function testCloneWithInvalidRepositoryReturnsFalse()
    {
        $tempDir = sys_get_temp_dir() . '/test_clone_' . uniqid();

        $result = $this->gitService->clone('https://invalid-repo-url.example.com/repo.git', $tempDir);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('output', $result);
        $this->assertArrayHasKey('error', $result);
        $this->assertArrayHasKey('exit_code', $result);
        $this->assertFalse($result['success']);
    }

    public function testStatusReturnsArrayWithExpectedKeys()
    {
        // Test with the current repository
        $result = $this->gitService->status(__DIR__ . '/../..');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('output', $result);
        $this->assertArrayHasKey('error', $result);
        $this->assertArrayHasKey('exit_code', $result);
    }

    public function testPullThrowsExceptionForNonRepository()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('is not a git repository');

        $this->gitService->pull('/path/that/does/not/exist');
    }

    public function testCommitThrowsExceptionForNonRepository()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('is not a git repository');

        $this->gitService->commit('/path/that/does/not/exist', 'Test commit');
    }

    public function testPushThrowsExceptionForNonRepository()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('is not a git repository');

        $this->gitService->push('/path/that/does/not/exist');
    }

    public function testPullReturnsArrayWithExpectedKeys()
    {
        // Test pull returns proper structure (may fail if no remote configured)
        $result = $this->gitService->pull(__DIR__ . '/../..');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('output', $result);
        $this->assertArrayHasKey('error', $result);
        $this->assertArrayHasKey('exit_code', $result);
    }

    public function testCommitReturnsArrayWithExpectedKeys()
    {
        // Test commit returns proper structure (will fail if nothing to commit)
        $result = $this->gitService->commit(__DIR__ . '/../..', 'Test commit message');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('output', $result);
        $this->assertArrayHasKey('error', $result);
        $this->assertArrayHasKey('exit_code', $result);
    }

    public function testPushReturnsArrayWithExpectedKeys()
    {
        // Test push returns proper structure (may fail if no remote configured)
        $result = $this->gitService->push(__DIR__ . '/../..');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('output', $result);
        $this->assertArrayHasKey('error', $result);
        $this->assertArrayHasKey('exit_code', $result);
    }
}
