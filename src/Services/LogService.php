<?php

namespace SteelAnts\LaravelBoilerplate\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use SplFileInfo;
use SteelAnts\LaravelBoilerplate\Helpers\SizeHelper;

class LogService
{
    /**
     * Files above this size are not rendered in the browser (download only)
     * and are skipped when counting today's stats.
     */
    public const MAX_OPEN_SIZE = 10 * 1024 * 1024;

    public const STATS_LEVELS = ['ERROR', 'WARNING', 'INFO', 'DEBUG'];

    public static function directory(): string
    {
        return storage_path('logs');
    }

    /**
     * Log files of all channels from logging.channels that have a "path" (single, daily, custom ...)
     * plus any other file in storage/logs (e.g. of a channel removed from the config), newest first.
     *
     * @return array<string, string[]> absolute path => names of the channels writing into it
     */
    public static function files(): array
    {
        $files = [];
        foreach (config('logging.channels', []) as $channel => $config) {
            foreach (self::channelFiles($config) as $path) {
                $files[$path][] = $channel;
            }
        }

        if (File::isDirectory(self::directory())) {
            foreach (File::files(self::directory()) as $file) {
                $files[$file->getRealPath()] ??= [];
            }
        }

        uksort($files, fn (string $a, string $b) => filemtime($b) <=> filemtime($a));

        return $files;
    }

    /**
     * Existing files of a channel; a daily channel "logs/debug.log" writes into "logs/debug-Y-m-d.log".
     */
    private static function channelFiles(array $config): array
    {
        $path = $config['path'] ?? null;
        if (!is_string($path)) {
            return [];
        }

        if (($config['driver'] ?? null) !== 'daily') {
            return is_file($path) ? [realpath($path)] : [];
        }

        $info = pathinfo($path);
        $pattern = $info['dirname'] . DIRECTORY_SEPARATOR . $info['filename'] . '-[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9]'
            . (isset($info['extension']) ? '.' . $info['extension'] : '');

        return array_map('realpath', glob($pattern) ?: []);
    }

    /**
     * List of ['fileName', 'channel', 'size', 'humanReadableSize', 'modified_at' => Carbon].
     */
    public static function list(): array
    {
        $items = [];
        foreach (self::files() as $path => $channels) {
            $file = new SplFileInfo($path);
            $items[] = [
                'fileName'          => $file->getFilename(),
                'channel'           => $channels ? implode(', ', $channels) : null,
                'size'              => $file->getSize(),
                'humanReadableSize' => SizeHelper::getHumanReadableSize($file->getSize()),
                'modified_at'       => Carbon::createFromTimestamp($file->getMTime())->setTimezone(config('app.timezone')),
            ];
        }

        return $items;
    }

    /**
     * STATS_LEVELS without DEBUG when no file channel logs at the debug level.
     */
    public static function statsLevels(): array
    {
        $debug = collect(config('logging.channels', []))
            ->filter(fn (array $config) => is_string($config['path'] ?? null))
            ->contains(fn (array $config) => strtolower($config['level'] ?? 'debug') === 'debug');

        return $debug ? self::STATS_LEVELS : array_values(array_diff(self::STATS_LEVELS, ['DEBUG']));
    }

    /**
     * Today's entry counts per level summed over all channels, null when a file is too large to scan.
     * Entries are matched by their "[Y-m-d" prefix, so it works for both single and daily files.
     */
    public static function todayStats(): array
    {
        $levels = self::statsLevels();
        $stats = array_fill_keys($levels, 0);
        $today = Carbon::now(config('app.timezone'))->startOfDay();
        $prefix = '[' . $today->format('Y-m-d');

        foreach (array_keys(self::files()) as $path) {
            if (filemtime($path) < $today->getTimestamp()) {
                continue;
            }

            if (filesize($path) > self::MAX_OPEN_SIZE) {
                return array_fill_keys($levels, null);
            }

            $handle = fopen($path, 'r');
            while (($line = fgets($handle)) !== false) {
                if (!str_starts_with($line, $prefix)) {
                    continue;
                }

                foreach ($levels as $level) {
                    if (str_contains($line, '.' . $level . ':')) {
                        $stats[$level]++;
                        break;
                    }
                }
            }
            fclose($handle);
        }

        return $stats;
    }

    /**
     * Absolute path of a log file listed by files(), null otherwise.
     */
    public static function path(string $filename): ?string
    {
        foreach (array_keys(self::files()) as $path) {
            if (basename($path) === $filename) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Complete lines written after the byte $offset, at most MAX_OPEN_SIZE bytes.
     * Returns ['lines' => string[], 'offset' => int], where offset is passed to the next call.
     * A partially written last line is left for the next call; a truncated file is read from the start.
     */
    public static function read(string $path, int $offset = 0): array
    {
        clearstatcache(true, $path);
        if ($offset > File::size($path)) {
            $offset = 0;
        }

        $handle = fopen($path, 'r');
        fseek($handle, $offset);
        $content = stream_get_contents($handle, self::MAX_OPEN_SIZE);
        fclose($handle);

        $end = strrpos($content, "\n");
        if ($end === false) {
            return ['lines' => [], 'offset' => $offset];
        }

        return [
            'lines'  => explode("\n", substr($content, 0, $end)),
            'offset' => $offset + $end + 1,
        ];
    }

    public static function delete(string $filename): bool
    {
        $path = self::path($filename);

        return $path !== null && File::delete($path);
    }

    /**
     * Clears all log files. Files touched today are truncated instead of deleted,
     * because long-running processes (queue workers) keep them open and would keep
     * writing into the deleted file.
     */
    public static function clear(): void
    {
        $today = Carbon::now(config('app.timezone'))->startOfDay()->getTimestamp();

        foreach (array_keys(self::files()) as $path) {
            if (filemtime($path) >= $today) {
                File::put($path, '');
            } else {
                File::delete($path);
            }
        }
    }
}
