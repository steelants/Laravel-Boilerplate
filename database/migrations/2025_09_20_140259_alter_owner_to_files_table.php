<?php

use App\Models\Activity;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
        Schema::table('files', function (Blueprint $table) {
			$table->string("fileable_type")->nullable()->change();
            $table->unsignedBigInteger("fileable_id")->nullable()->change();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::table('files', function (Blueprint $table) {
			$table->string("fileable_type")->nullable(false)->change();
            $table->unsignedBigInteger("fileable_id")->nullable(false)->change();
		});
	}
};
