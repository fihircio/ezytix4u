<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePromocodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promocodes', function (Blueprint $table) {
            $table->id();

           
            $table->string('code', 32)->unique();
            $table->double('reward', 10, 2)->nullable();
            $table->integer('quantity')->nullable();
            $table->string('p_type', 32)->default('fixed');

            $table->text('data')->nullable();

            $table->boolean('is_disposable')->default(false);
            $table->timestamp('expires_at')->nullable();

            $table->boolean('status')->default(1);
            $table->timestamps();
        });

        Schema::create('promocode_user', function (Blueprint $table) {

            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('promocode_id');
            $table->unsignedInteger('ticket_id');

            $table->timestamp('used_at');

            $table->unique(['user_id', 'promocode_id', 'ticket_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('promocode_user');
        Schema::drop('promocodes');
    }
}
