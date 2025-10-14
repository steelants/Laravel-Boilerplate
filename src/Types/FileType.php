<?php

namespace SteelAnts\LaravelBoilerplate\Types;

class FileType
{
    const INLINE = 0;
    const ATTACHMENT = 1;

    public static function getNames()
    {
        return $names = [
            self::INLINE     => __('Text'),
            self::ATTACHMENT => __('Attachment'),
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
}
