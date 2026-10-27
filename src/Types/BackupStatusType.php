<?php

namespace SteelAnts\LaravelBoilerplate\Types;

class BackupStatusType
{
    const QUEUED = 'queued';

    const RUNNING = 'running';

    const FINISHED = 'finished';

    const FAILED = 'failed';

    public static function getNames()
    {
        return $names = [
            self::QUEUED   => __('Backup is waiting in the queue...'),
            self::RUNNING  => __('Backup is running...'),
            self::FINISHED => __('Backup finished'),
            self::FAILED   => __('Backup failed'),
        ];
    }

    public static function getName($type)
    {
        $names = self::getNames();
        if (isset($names[$type])) {
            return __($names[$type]);
        }

        return __('NULL');
    }

    public static function getkeys()
    {
        return array_keys(self::getNames());
    }

    public static function isInProgress($type): bool
    {
        return in_array($type, [self::QUEUED, self::RUNNING]);
    }
}
