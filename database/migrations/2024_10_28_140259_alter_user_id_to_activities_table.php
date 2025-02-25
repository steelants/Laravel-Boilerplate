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
		if (!Schema::hasColumn('activities', 'actor')) {
			Schema::table('activities', function (Blueprint $table) {
				$table->nullableMorphs(name: 'actor');
			});
		}

		$activities = Activity::all();
		if (!empty($activities) && $activities->count() > 0) {
			foreach ($activities as $activity) {
				if (empty($activity->user_id)) {
					continue;
				}
				$user = User::find($activity->user_id);
				if (empty($user) || $user->count() <= 0) {
					continue;
				}
				$activity->actor()->associate($user);
				$activity->save();
			}
		}

		Schema::table('activities', function (Blueprint $table) {
			$table->dropForeign(['user_id']);
			$table->dropColumn('user_id');
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::table('activities', function (Blueprint $table) {
			$table->foreignId("user_id")->nullable();
			$table->foreign("user_id")->references("id")->on("users")->constrained();
		});

		$activities = Activity::all();
		if (!empty($activities) && $activities->count() > 0) {
			foreach ($activities as $activity) {
				$actor = $activity->actor?->getModel();
				if (empty($actor) || $actor->count() <= 0 || get_class($actor) != User::class) {
					continue;
				}
				$activity->user_id = $actor->id;
				$activity->save();
			}
		}

		Schema::table('activities', function (Blueprint $table) {
			$table->dropMorphs('actor');
		});
	}
};
