<?php

namespace App\Helpers;

class SizeHelper
{
	public static function getHumanReadableSize($bytes)
    {
        if ($bytes > 0) {
            $base = floor(log($bytes) / log(1024));
            $units = array(
                "B",
                "KB",
                "MB",
                "GB",
                "TB",
                "PB",
                "EB",
                "ZB",
                "YB",
            ); //units of measurement
            return number_format(($bytes / pow(1024, floor($base))), 3) . " $units[$base]";
        } else {
            return "0 bytes";
        }
    }

	public static function humanFileSize($bytes, $decimals = 2)
    {
        $sz = 'BKMGTP';
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
    }

    public static function returnBytes($size_str)
    {
        switch (substr($size_str, -1)) {
            case 'M':
            case 'm':
                return (int) $size_str * 1048576;
            case 'K':
            case 'k':
                return (int) $size_str * 1024;
            case 'G':
            case 'g':
                return (int) $size_str * 1073741824;
            default:
                return $size_str;
        }
    }
}
