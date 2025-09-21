<?php

namespace SteelAnts\LaravelBoilerplate\Types;

class SettingDataType
{
    const STRING = "0";
    const INT = "1";
    const BOOL = "2";
    const MODEL = "3";
    const SELECT = "4";
    const PASSWORD = "5";
    const SENSITIVE = "6";
    const JSON = "7";
    const FILE = "8";
    const DATE = "9";

    public static function getTypes()
    {
        return $types = [
            self::STRING   => "text",
            self::INT      => "number",
            self::BOOL     => "checkbox",
            self::MODEL    => "model",
            self::SELECT   => "select",
            self::PASSWORD => "password",
            self::JSON     => "json",
            self::FILE     => "file",
            self::DATE     => "date",
        ];
    }

    public static function getType($type)
    {
        return self::getTypes()[$type] ?? 'NULL';
    }
}
