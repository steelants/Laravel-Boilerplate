<?php

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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('settable');
            $table->string('index')->indexed();
            $table->string('type', 50)->default('string'); //TODO: @Vasek Maybe use Set
            $table->text('value');
            $table->timestamps();
            $table->unique(['settable_type', 'settable_id', 'index']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
