<?php

namespace SteelAnts\LaravelBoilerplate\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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
		foreach (['database', 'storage'] as $backupPath) {
			$command = "rm -f " . storage_path('app/backups') . "/" . date("Y-m-d", strtotime('-' . $days . ' days')) . '_' . $backupPath . ".zip";
			exec($command, $output);
		}
		Log::info('Clean Old backups ' . $days . ' old');

		///DATABASE
		if (config('boilerplate.backup.database')) {
			if (config('database.default') == 'sqlite') {
				$dbFile = database_path('database.sqlite');
				$dbName = basename($dbFile, ".php");
				$backupFile = $db_backup_path . '/' . $dbName . '_' . date("Y-m-d", time()) . '.sqlite';
				$command = "cp $dbFile $backupFile 2>&1";
				exec($command, $output);
				Log::info('Backup ' . $dbName . ' db ');
				Log::Debug($output);
			} else if (config('database.default') == 'pgsql') {
				$dbHost = config('database.connections.pgsql.host');
				$dbName = config('database.connections.pgsql.database');
				$dbUserName = config('database.connections.pgsql.username');
				$dbPassword = config('database.connections.pgsql.password');

				foreach (['data', 'scheme'] as $type) {
					$parameters = "--schema-only";
					if ($type == "data") {
						$parameters = "--data-only";
					}

					putenv("PGPASSWORD=" . $dbPassword);

					$backupFile = $db_backup_path . '/' . $dbName . '_' . $type . '_' . date("Y-m-d", time()) . '.sql';
					$command = "pg_dump --no-comments $parameters -h $dbHost -U $dbUserName -d $dbName -f \"$backupFile\" 2>&1";
					exec($command, $output);
					Log::info('Backup ' . $dbName . ' db ' . $type);
					Log::Debug($output);
				}
			} else {
				$dbHost = config('database.connections.mysql.host');
				$dbName = config('database.connections.mysql.database');
				$dbUserName = config('database.connections.mysql.username');
				$dbPassword = config('database.connections.mysql.password');

				foreach (['data', 'scheme'] as $type) {
					$parameters = "--no-data";
					if ($type == "data") {
						$parameters = "--no-create-info";
					}

					$backupFile = $db_backup_path . '/' . $dbName . '_' . $type . '_' . date("Y-m-d", time()) . '.sql';
					$command = "mysqldump --skip-ssl --skip-comments " . $parameters . " -h " . $dbHost . " -u " . $dbUserName . " -p" . $dbPassword . " " . $dbName . " -r $backupFile 2>&1";
					exec($command, $output);
					Log::info('Backup ' . $dbName . ' db ' . $type);
					Log::Debug($output);
				}
			}
		}

		if (config('boilerplate.backup.storage')) {
			//STORAGE
			foreach (config('boilerplate.backup.storage_paths') as $storage_path) {
				Log::info('storage backup done');
				Log::Debug($output);
				$command = "cp -R " . storage_path($storage_path) . " " . storage_path('backups/tmp/storage');
				exec($command, $output);
			}
		}

		if (config('boilerplate.backup.enviroment')) {
			//Backupo .env
			$envBackupFile = storage_path("backups/tmp/storage/env.backup");
			$envSourceFile = app()->environmentFilePath();

			$command = "cp " . $envSourceFile . " " . $envBackupFile;
			exec($command, $output);
			Log::info('Backup .env');
		}

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

			$command = "cd " . $backupPath . "; zip -rm " . $zippedFilePath . " ./*";
			exec($command, $output);
			Log::info($backupPath . '=>' . $zippedFilePath);

			$command = "md5sum " . $zippedFilePath;
			exec($command, $output);
			Log::info('Zipping hash');

			$charSet = preg_replace(array('/\s{2,}/', '/[\t\n]/'), ' ', $output[count($output) - 1]);
			$charSet = rtrim($charSet);

			$fileMD5Hash = explode(" ", $charSet)[0];
			Log::debug($fileMD5Hash);
			Log::info($backupPath . '=>' . $zippedFilePath . '=>' . $fileMD5Hash);
		}


		if (!empty(config('boilerplate.system_admins_mail')) && config('boilerplate.system_admins_mail') != "") {
			Mail::raw(__('Backup Run successfully'), function ($message) {
				$message->to(config('boilerplate.system_admins_mail'))->subject(__('Backup Run successfully ') . config('app.name'));
			});
			Log::info('Sending Notification');
		}
	}

	private function execShellCommand($command, &$output)
	{
		$output = null;
		exec($command, $output);
		Log::debug($output);
	}
}
