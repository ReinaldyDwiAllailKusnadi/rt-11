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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resident_id')->nullable()->constrained('residents')->onDelete('cascade');
            $table->string('type'); // kas, keamanan, kemalangan, sakit, bayarSATPAM, konsumsiRAPAT, lainLAIN
            $table->integer('amount');
            $table->date('date');
            $table->text('keterangan')->nullable();
            $table->string('nama_satpam')->nullable();
            $table->json('bulan_list')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
