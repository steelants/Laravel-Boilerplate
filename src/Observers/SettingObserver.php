<?php

namespace SteelAnts\LaravelBoilerplate\Observers;

use App\Types\SettingDataType;
use Carbon\Carbon;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use SteelAnts\LaravelBoilerplate\Models\File as ModelsFile;
use SteelAnts\LaravelBoilerplate\Models\Setting;

class SettingObserver
{
    public function creating(Setting $setting)
    {
        if ($setting->type == SettingDataType::PASSWORD || $setting->type == SettingDataType::SENSITIVE) {
            $setting->value = Crypt::encryptString($setting->value);
        }
    }

    public function updated(Setting $setting)
    {
        $setting->updated_at = Carbon::now()->toDateTimeString();
        if ($setting->type == SettingDataType::PASSWORD || $setting->type == SettingDataType::SENSITIVE) {
            $setting->value = Crypt::encryptString($setting->value);
        } elseif ($setting->type == SettingDataType::FILE) {
            if ($setting->isDirty('value')) {
                if ($setting->value != $setting->getOriginal('value')) {
                    $pathArr = array_filter(explode('/', $setting->getOriginal('value')));
                    $path = explode("/", str_replace("/files/", "", str_replace("-", DIRECTORY_SEPARATOR, $setting->getOriginal('value'))))[0] . "/" . $pathArr[3];
                    $file = ModelsFile::where("path", $path)->first();
                    if (!empty($file)) {
                        $file->delete();
                    }
                }
            }
        }
    }

    public function retrieved(Setting $setting)
    {
        if ($setting->type == SettingDataType::PASSWORD || $setting->type == SettingDataType::SENSITIVE) {
            try {
                $setting->value = Crypt::decryptString($setting->value);
            } catch (DecryptException $e) {
            }
        }
    }
}
