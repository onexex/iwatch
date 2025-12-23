<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sms_messages', function (Blueprint $table) {
            // This updates the existing 'message' column to have a default value
            $table->text('message')->default('No message content')->change();
            
            // Optional: If you want to make received_at default to current time as well
            $table->timestamp('received_at')->useCurrent()->change();
        });
    }

    public function down(): void
    {
        Schema::table('sms_messages', function (Blueprint $table) {
            // To rollback, we remove the default
            $table->text('message')->default(null)->change();
            $table->timestamp('received_at')->useCurrent(false)->change();
        });
    }
};