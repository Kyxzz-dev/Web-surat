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
            $table->string('letter_nature');
            $table->date('letter_date');
            $table->text('description');
            $table->text('note')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->enum('letter_nature', ['Rahasia', 'Sangat Rahasia', 'Penting', 'Sangat Penting'])->after('reference_number');
            // Field khusus untuk surat keluar - harus nullable
            $table->string('agenda_number')->nullable();
            $table->date('received_date')->nullable();
            $table->unsignedBigInteger('classification_id')->nullable();
            $table->unsignedBigInteger('sub_classification_id')->nullable();
            
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('classification_id')->references('id')->on('classifications');
            $table->foreign('sub_classification_id')->references('id')->on('sub_classifications');
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
