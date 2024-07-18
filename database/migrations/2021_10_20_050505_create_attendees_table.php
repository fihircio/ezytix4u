<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendees', function (Blueprint $table) {
            $table->id();
            $table->integer('seat_id')->unsigned()->nullable();
            $table->integer('ticket_id')->unsigned();
            $table->integer('event_id')->unsigned();
            $table->integer('booking_id')->unsigned();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('seat_name')->nullable();
            $table->boolean('status')->default(1);
            $table->boolean('checked_in')->default(0);
            $table->timestamps();

            $table->foreign('seat_id')->references('id')->on('seats')->onDelete('cascade');
            $table->foreign('ticket_id')->references('id')->on('tickets')->onDelete('cascade');
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendees', function (Blueprint $table) {
            $table->dropForeign('seat_id');
            $table->dropForeign('ticket_id');
            $table->dropForeign('event_id');
            $table->dropForeign('booking_id');
        });
        Schema::dropIfExists('attendees');
    }
}
