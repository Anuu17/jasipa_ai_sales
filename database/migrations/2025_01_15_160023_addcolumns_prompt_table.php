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
        Schema::table('prompt', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->comment('会社ID')->after('user_id');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prompt', function (Blueprint $table) {
            $table->dropColumn('company_id');
        });
    }
};
