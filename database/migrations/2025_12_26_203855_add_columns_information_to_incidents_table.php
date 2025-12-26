<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Classification;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->foreignIdFor(Classification::class);
            $table->string('file_number')->nullable();
            $table->string('reference')->nullable();
            $table->string('subject')->nullable();

            $table->date('date_of_report')->nullable();
            $table->string('reporter')->nullable();
            $table->string('designation')->nullable();

            $table->text('evaluation')->nullable();
            $table->string('source')->nullable();
            $table->date('date_acquired')->nullable();
            $table->string('manner_acquired')->nullable();

            $table->text('information_proper')->nullable();
            $table->text('analysis')->nullable();
            $table->text('actions')->nullable();

            $table->json('attachment')->nullable();

            $table->foreignIdFor(User::class, 'created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->dropColumn([
                'classification_id',
                'file_number',
                'reference',
                'subject',
                'date_of_report',
                'reporter',
                'designation',
                'evaluation',
                'source',
                'date_acquired',
                'manner_acquired',
                'information_proper',
                'analysis',
                'actions',
                'attachment',
                'created_by'
            ]);
        });
    }
};
