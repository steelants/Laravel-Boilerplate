<?php

namespace App\Jobs;

use Directory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class Backup implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
    }

    public function handle()
    {
        //PREPARATION
        ini_set('date.timezone', 'Europe/Prague');
        $days = 3;
        $dbName = config('database.connections.mysql.database');
        $dbUserName = config('database.connections.mysql.username');
        $dbPassword = config('database.connections.mysql.password');

        $fs_backup_path = storage_path('backups/tmp/storage');
        $db_backup_path = storage_path('backups/tmp/db');

        //TODO: verifi all folders exists
        foreach ([$db_backup_path, $fs_backup_path] as $backupPath) {
            if (!File::exists($backupPath)) {
                File::makeDirectory($backupPath, 0755, true);
            } else {
                $command = "rm -r -f " . $backupPath . "/*";
                exec($command, $output);
                Log::Info('Clean Old Temp ' . $backupPath);
                Log::Debug($output);
            }
        }

        //REMOVE OLD BACKUPS
        $command = "rm -f " . storage_path('app/backups') . "/" . date("Y-m-d", strtotime('-' . $days . ' days')) . ".zip";
        exec($command, $output);
        Log::info('Clean Old backups ' . $days . ' old');

        //DATABASE
        foreach (['data', 'scheme'] as $type) {
            $parameters = "--no-data";
            if ($type == "data") {
                $parameters = "--no-create-info";
            }

            $backupFile = $db_backup_path . '/' . $dbName  . '_' . $type . '_' . date("Y-m-d", time()) . '.sql';
            $command = "mysqldump --skip-comments " . $parameters . " -h localhost -u " . $dbUserName . " -p" . $dbPassword  . " " . $dbName  . " -r $backupFile 2>&1";
            exec($command, $output);
            Log::info('Backup ' . $dbName . ' db ' . $type);
            Log::Debug($output);
        }

        //STORAGE
        $command = "cp -R " . storage_path('app') . " " . storage_path('backups/tmp/storage');
        exec($command, $output);
        Log::info('storage backup done');
        Log::Debug($output);

        //Backupo .env
        $envBackupFile = storage_path("backups/tmp/storage/env.backup");
        $envSourceFile = app()->environmentFilePath();

        $command = "cp " . $envSourceFile . " " . $envBackupFile;
        exec($command, $output);
        Log::info('Backup .env');

        //Clear previouse backups from same day
        $command = "rm -f " . storage_path('backups') . "/" . date("Y-m-d", time()) . ".zip";
        exec($command, $output);
        Log::info('Clean previous backup');

        foreach (['database' => $db_backup_path, 'storage' => $fs_backup_path] as $filename => $backupPath) {
            $zippedFilePath = storage_path('backups/' . date("Y-m-d", time()) . '_' . $filename . ".zip");

            if (File::exists($zippedFilePath)) {
                $command = "rm -r -f " . $zippedFilePath;
                exec($command, $output);
                Log::Info('Clean Old Backup File' . $zippedFilePath);
                Log::Debug($output);
            }

            $command = "cd ".$backupPath."; zip -rm ".$zippedFilePath." ./*" ;
            exec($command, $output);
            Log::info($backupPath . '=>' . $zippedFilePath);

            $command = "md5sum ".  $zippedFilePath;
            exec($command, $output);
            Log::info('Zipping hash');

            $charSet = preg_replace(array('/\s{2,}/', '/[\t\n]/'), ' ', $output[count($output)-1]);
            $charSet = rtrim($charSet);

            $fileMD5Hash = explode(" ", $charSet)[0];
            Log::debug($fileMD5Hash);
            echo($backupPath . '=>'.$zippedFilePath.'=>' .$fileMD5Hash. "\n");
        }

        if (!empty(env('APP_ADMIN'))) {
            Mail::raw(__('Backup Run successfully'), function ($message) {
                $message->to('vasek@steelants.cz')->subject(_('Backup Run successfully ') . env('APP_NAME'));
            });
            Log::info('Sending Notification');
        }
    }

    private function execShellCommand($command, &$output)
    {
        $output = $null;
        exec($command, $output);
        Log::debug($output);
    }
}
