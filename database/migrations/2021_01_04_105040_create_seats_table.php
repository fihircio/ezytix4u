<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seats', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('seatchart_id')->unsigned();
            $table->integer('ticket_id')->unsigned();
            $table->integer('event_id')->unsigned();
            $table->boolean('status')->default(1);
            $table->string('coordinates');
            $table->string('name');
            $table->timestamps();

            $table->foreign('seatchart_id')->references('id')->on('seatcharts')->onDelete('cascade');
            $table->foreign('ticket_id')->references('id')->on('tickets')->onDelete('cascade');
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('seats', function (Blueprint $table) {
            $table->dropForeign('seatchart_id');
            $table->dropForeign('ticket_id');
            $table->dropForeign('event_id');
        });
        Schema::dropIfExists('seats');
    }
}
