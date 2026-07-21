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
    public function up(): void
    {
        Schema::create('letters', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // incoming/outgoing
            $table->string('reference_number')->unique();
            $table->string('from')->nullable(); // nullable karena tidak diperlukan untuk outgoing
            $table->string('to')->nullable(); // nullable karena tidak diperlukan untuk incoming
            $table->enum('letter_nature', ['Segera','Sangat Segera','Biasa','Rahasia', 'Sangat Rahasia']);
            $table->date('letter_date');
            $table->text('description');
            $table->text('note')->nullable();
            // Field khusus untuk surat keluar - harus nullable
            $table->string('agenda_number')->nullable();
            $table->date('received_date')->nullable();
            $table->string('letter_code')->nullable();
            $table->unsignedBigInteger('user_id');
            
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('letters');
    }
};
