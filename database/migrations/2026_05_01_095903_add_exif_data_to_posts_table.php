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
         Schema::table('posts', function (Blueprint $table) {
            // stocke  les données EXIF dans un champ JSON
            // nullable = EXIF facultatives.
            $table->json('exif_data')->nullable()->after('image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
             // supprime la colonne exif_data
            $table->dropColumn('exif_data');
        });
    }
};
