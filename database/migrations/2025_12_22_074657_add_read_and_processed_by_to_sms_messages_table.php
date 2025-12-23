<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sms_messages', function (Blueprint $table) {
            // Adds is_read with a default of false
            $table->boolean('is_read')->default(false)->after('message');
            
            // Adds processed_by (nullable because it starts empty)
            $table->string('processed_by')->nullable()->after('is_read');
        });
    }

    public function down(): void
    {
        Schema::table('sms_messages', function (Blueprint $table) {
            $table->dropColumn(['is_read', 'processed_by']);
        });
    }
};