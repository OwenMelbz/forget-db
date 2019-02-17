<?php

namespace App\Services;

use Phar;
use Exception;
use Github\Client as GitHubClient;

/**
 * Class UpdateService
 * @package App\Services
 */
class UpdateService
{
    /**
     * Which user owns the repo to download the updates from.
     */
    public const GITHUB_USER = 'kissg1988';

    /**
     * The name of the repo to download updates from.
     */
    public const GITHUB_REPO = 'forget-db';

    /**
     * @var GitHubClient
     */
    private $github;

    /**
     * UpdateService constructor.
     */
    public function __construct()
    {
        $this->github = new GitHubClient;
    }

    /**
     * Determines if the user should be using the updater,
     * typically only if you're using the self-contained
     * version should you be using it.
     *
     * @return string
     */
    public function shouldUpdate(): string
    {
        return Phar::running();
    }

    /**
     * Will check if the latest version on github
     * is newer than the version installed,
     * if it is then it will return the latest information,
     * which can then be installed later on!
     *
     * @return null|object
     */
    public function searchForUpdates()
    {
        $installedVersion = config('app.version');

        $remoteBuild = $this->getLatestRelease();

        if (!$remoteBuild) {
            return null;
        }

        if (!version_compare($installedVersion, data_get($remoteBuild, 'tag_name'), '<')) {
            return null;
        }

        return (object) [
            'release' => $remoteBuild,
            'download' => $this->getBuild(data_get($remoteBuild, 'id')),
        ];
    }

    /**
     * Here we rename any old copies as a backup, then we download the
     * newest version from github, if it happens okay then all good,
     * otherwise it will try put hte other version back!
     *
     * @param $update
     * @return string
     * @throws Exception
     */
    public function updateTo($update): string
    {
        $fileUrl = data_get($update, 'download.browser_download_url');

        $data = @file_get_contents($fileUrl);

        $fileToOverWrite = $this->fileToOverWrite();

        if (file_exists($fileToOverWrite)) {
            @rename($fileToOverWrite, $fileToOverWrite . '.backup');
        }

        @file_put_contents($fileToOverWrite, $data);

        @chmod($fileToOverWrite, 0755);

        if (file_exists($fileToOverWrite)) {
            return $fileToOverWrite;
        }

        @rename($fileToOverWrite . '.backup', $fileToOverWrite);

        throw new Exception('Hmm could not download the file, looks like there are some permission issues, you can manually download the update from ' . $fileUrl);
    }

    /**
     * Returns back the latest release from GitHub
     *
     * @return array
     */
    public function getLatestRelease(): array
    {
        return $this
            ->github
            ->api('repo')
            ->releases()
            ->latest(static::GITHUB_USER, static::GITHUB_REPO);
    }

    /**
     * Returns the downloadable asset from GitHub
     *
     * @param int $releaseId
     * @return array
     */
    public function getBuild(int $releaseId): array
    {
        $build = $this->github
            ->api('repo')
            ->releases()
            ->assets()
            ->all(static::GITHUB_USER, static::GITHUB_REPO, $releaseId);

        return current($build);
    }

    /**
     * More of a helper function to make sure it overwrites,
     * the correct file when in development.
     *
     * @return string
     */
    public function fileToOverWrite(): string
    {
        if ($this->shouldUpdate()) {
            return Phar::running(false);
        }

        return 'forget-db.phar';
    }
}
