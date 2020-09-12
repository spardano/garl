<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableKelurahan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kelurahan', function (Blueprint $table) {
            $table->id();
            $table->integer("kecamatan_id");
            $table->string('name');
            $table->integer('area')->default(0);
            $table->longText('data')->nullable();
            $table->longText('polygon');
            $table->string('class')->nullable();
            $table->timestamps();

            $table->index('kecamatan_id');
        });
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
