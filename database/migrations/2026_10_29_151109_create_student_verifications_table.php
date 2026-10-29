
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_verifications', function (Blueprint $table) {
            $table->id();

            $table->string('id_mhs', 50)->unique();

            $table->string('status_verifikasi', 20)
                ->default('Pending');

            $table->text('catatan')->nullable();

            $table->string('verified_by')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_verifications');
    }
};
