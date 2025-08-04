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
        Schema::create('letter_slots', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->integer('slot_number'); 
            $table->boolean('is_filled')->default(false);
            $table->foreignId('letter_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
        });
                
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()  
    {
        Schema::dropIfExists('letter_slots');
    }
};
