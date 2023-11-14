<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddTenantToAllTables extends Migration
{
    private $skipTables = ['jobs', 'failed_jobs', 'users', 'migrations'];
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tables = DB::select('SHOW TABLES');
        foreach ($tables as $table) {
            if (in_array($table->Tables_in_db_name, $this->skipTables)) {
                continue;
            }
            Schema::table($table->Tables_in_db_name, function ($table) {
                $table->foreignIdFor(Tenant::class)->constrained();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tables = DB::select('SHOW TABLES');
        foreach ($tables as $table) {
            if (in_array($table->Tables_in_db_name, $this->skipTables)) {
                continue;
            }
            Schema::table('users', function ($table) {
                $table->dropColumn('tenant_id');
            });
        }
    }
}
