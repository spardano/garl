<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableProvince extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kabupaten', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('center_point')->nullable();
            $table->timestamps();
        });
        // $table->index(['account_id', 'created_at']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
