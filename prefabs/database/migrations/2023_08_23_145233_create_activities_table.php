<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('ip')->nullable();
            $table->integer('quiz_id')->nullable();
            $table->foreignId("user_id")->nullable();
            $table->foreign("user_id")->references("id")->on("users")->constrained();
            $table->nullableMorphs('affected');
            $table->string('lang_text')->nullable();
            $table->json('data')->nullable();
            $table->timestamp("created_at");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
