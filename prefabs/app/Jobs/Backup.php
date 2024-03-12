<?php

namespace App\Jobs;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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

        //TODO: verifi all folders exists
        foreach (['/backups/tmp/db', '/backups/tmp/storage'] as $backupPath) {
            if (!Storage::exists($backupPath)) {
                Storage::makeDirectory($backupPath);
            } else {
                $command = "rm -r -f " . storage_path($backupPath);
                exec($command, $output);
                Log::Info('Clean Old Temp ' . $backupPath);
                Log::Debug($output);
            }
        }

        //REMOVE OLD BACKUPS
        die();

        $command = "rm -f " . storage_path('backups') . "/" . date("Y-m-d", strtotime('-' . $days . ' days')) . ".zip";
        exec($command, $output);
        Log::info('Clean Old backups ' . $days . ' old');

        //DATABASE
        foreach (['data', 'scheme'] as $type) {
            $parameters = "--no-data";
            if($type == "data"){
                $parameters = "--no-create-info";
            }
                
            $backupFile = storage_path('/backups/tmp/db' . $dbName  . '_' . $type . '_' . date("Y-m-d", time()) . '.sql');
            $command = "mysqldump --skip-comments " . $parameters . " -h localhost -u " . $dbUserName . " -p" . $dbPassword  . " " . $dbName  . " -r $backupFile 2>&1";
            exec($command, $output);
            Log::info('Backup ' . $dbName . ' db ' . $type);
            Log::Debug($output);
        }

        //STORAGE
        $command = "cp -R " . storage_path('app') . " " . storage_path('/backups/tmp/storage');
        exec($command, $output);
        Log::info('storage backup done');
        Log::Debug($output);

        //Backupo .env
        $envBackupFile = storage_path("backups/tmp/env.backup");
        $envSourceFile = app()->environmentFilePath();

        $command = "cp " . $envSourceFile . " " . $envBackupFile;
        exec($command, $output);
        Log::info('Backup .env');

        //Clear previouse backups from same day
        $command = "rm -f " . storage_path('backups') . "/" . date("Y-m-d", time()) . ".zip";
        exec($command, $output);
        Log::info('Clean previous backup');

        $command = "cd " . storage_path('backups') . "/tmp/ && zip -rm " . "../" . date("Y-m-d", time()) . ".zip " . "./*";
        exec($command, $output);
        Log::info('Zipping Files');

        $command = "cd " . storage_path('backups') . "/tmp/ && md5sum ../" . date("Y-m-d", time()) . ".zip ";
        exec($command, $output);
        Log::info('Zipping hash');
        Log::debug($output);

        

        if (!empty(env('APP_ADMIN'))) {
            Mail::raw(__('Backup Run successfully'), function ($message) {
                $message->to('vasek@steelants.cz')->subject(_('Backup Run successfully ') . env('APP_NAME'));
            });
            Log::info('Sending Notification');
        }
    }
}
