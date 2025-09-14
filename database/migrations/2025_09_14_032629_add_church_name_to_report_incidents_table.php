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
        Schema::table('report_incidents', function (Blueprint $table) {
            $table->string('church_name')->after('incident_time')->nullable();
            $table->string('church_address')->after('church_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('report_incidents', function (Blueprint $table) {
            //
        });
    }
};
