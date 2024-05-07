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
        Schema::create('merge_scan_bookings', function (Blueprint $table) {
            $table->id();
            $table->integer('booking_id')->unsigned();
            $table->integer('event_id')->unsigned();
            $table->string('event_start_date');
            $table->timestamps();

            $table->foreign('event_id')->references('id')->on('events')
            ->onDelete('cascade');

            $table->foreign('booking_id')->references('id')->on('bookings')
            ->onDelete('cascade');;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('merge_scan_bookings');
    }
};
