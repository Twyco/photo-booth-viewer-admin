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
        Schema::create('album_access_codes', function (Blueprint $table) {
            $table->id();
            $table->uuid('album_uuid');
            $table->string('access_code')->unique();
            $table->integer('uses')->default(0);
            $table->timestamps();

            $table->foreign('album_uuid')
                ->references('uuid')
                ->on('albums')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('album_access_codes');
    }
};
