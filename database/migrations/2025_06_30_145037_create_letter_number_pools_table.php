<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('letter_number_pools', function (Blueprint $table) {
            $table->id();
            $table->date('date'); // Tanggal penyediaan nomor
            $table->string('number', 20); // Misal: 0001, 0002 ...
            $table->boolean('is_used')->default(false); // Status penggunaan
            $table->timestamps();
    
            $table->unique(['date', 'number']); // Unik per tanggal
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('letter_number_pools');
    }
};
