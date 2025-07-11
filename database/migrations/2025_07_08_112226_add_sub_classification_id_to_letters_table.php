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
        Schema::table('letters', function (Blueprint $table) {
            $table->foreignId('sub_classification_id')
                  ->nullable()
                  ->constrained('sub_classifications')
                  ->cascadeOnUpdate()
                  ->nullOnDelete(); // Jika sub-klasifikasi dihapus, set null
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('letters', function (Blueprint $table) {
            $table->dropForeign(['sub_classification_id']);
            $table->dropColumn('sub_classification_id');
        });
    }
};
