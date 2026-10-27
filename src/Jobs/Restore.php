<?php

namespace SteelAnts\LaravelBoilerplate\Jobs;

use Carbon\Carbon;
use Illuminate\Foundation\Bus\Dispatchable;
use SteelAnts\LaravelBoilerplate\Services\BackupService;

/**
 * Zpetna kompatibilita pro projekty, ktere maji publikovany starsi BackupController
 * volajici Restore::dispatchSync(). Uz to neni queued job (ani AllowManualRun) - obnova .env
 * nebo databaze by pod bezicim workerem zmenila frontu, APP_KEY nebo tabulku jobs a worker
 * by se mohl zaseknout. Obnova bezi synchronne pres BackupService::restore().
 * TODO: deprecovat behem roku 2027 a odstranit v dalsi major verzi.
 *
 * @deprecated Pouzij BackupService::restore().
 */
class Restore
{
    use Dispatchable;

    public function __construct(
        public Carbon $backup_date,
        public bool $restoreDatabase = true,
        public bool $restoreStorage = true,
        public bool $restoreEnv = false,
    ) {}

    public function handle()
    {
        BackupService::restore($this->backup_date->format('Y-m-d'), $this->restoreDatabase, $this->restoreStorage, $this->restoreEnv);
    }
}
