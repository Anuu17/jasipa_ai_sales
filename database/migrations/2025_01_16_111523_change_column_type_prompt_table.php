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
            $table->text('message')->comment('メッセージ保存コラム')->after('id')->change();
            $table->unsignedBigInteger('user_id')->comment('会社ID')->after('message')->change();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prompt', function (Blueprint $table) {
            $table->string('user_id')->change();
            $table->string('message')->change();
        });
    }
};
