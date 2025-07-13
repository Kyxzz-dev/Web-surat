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
    { Schema::table('sub_classifications', function (Blueprint $table) {
            $table->dropUnique('sub_classifications_code_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
          Schema::table('sub_classifications', function (Blueprint $table) {
            $table->unique('code'); // untuk rollback
        });
    }
};
