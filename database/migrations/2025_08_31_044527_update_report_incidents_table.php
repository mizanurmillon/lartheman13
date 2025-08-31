<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('report_incidents', function (Blueprint $table) {
            // Remove old columns
            $table->dropColumn(['title', 'location']);

            // Add new columns
            $table->foreignId('incident_type_id')->nullable()->after('category_id')->constrained('incident_types')->onDelete('cascade');
            $table->string('incident_type_other')->nullable()->after('incident_type_id');
            $table->foreignId('location_id')->after('description')->constrained('locations')->onDelete('cascade');
            $table->enum('share_regionally_mode', ['own_region', 'other_region'])->default('own_region')->after('location_id');
        });
    }

    public function down(): void
    {
        Schema::table('report_incidents', function (Blueprint $table) {
            $table->string('title')->after('category_id');
            $table->string('location')->after('description');

            $table->dropForeign(['incident_type_id']);
            $table->dropColumn(['incident_type_id', 'incident_type_other', 'location_id', 'share_regionally_mode']);
        });
    }
};
