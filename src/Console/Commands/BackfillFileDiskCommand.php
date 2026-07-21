<?php

namespace SteelAnts\LaravelBoilerplate\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use SteelAnts\LaravelBoilerplate\Models\File;

/**
 * Opt-in nástroj pro projekty, které si nejsou jisté defaultem 'local' z migrace.
 * Dohledá soubory reálně uložené na disku 'public' a opraví jim záznam. Idempotentní, lze pustit opakovaně.
 * Není součástí povinného deploy kroku.
 */
class BackfillFileDiskCommand extends Command
{
    protected $signature = 'boilerplate:backfill-file-disk';

    protected $description = 'Backfill the files.disk column by checking where files actually live on storage';

    public function handle(): int
    {
        $total = File::query()->where('disk', 'local')->count();

        if ($total === 0) {
            $this->components->info('Nothing to backfill, all files already have a disk assigned.');

            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($total);
        $updated = 0;

        File::query()
            ->where('disk', 'local')
            ->chunkById(500, function ($files) use ($bar, &$updated) {
                foreach ($files as $file) {
                    $key = trim($file->path, '/') . '/' . $file->filename;

                    if (Storage::disk('public')->exists($key)) {
                        $file->update(['disk' => 'public']);
                        $updated++;
                    }

                    $bar->advance();
                }
            });

        $bar->finish();
        $this->newLine(2);
        $this->components->info("Backfill done, moved {$updated} file(s) to disk=public.");

        return self::SUCCESS;
    }
}
