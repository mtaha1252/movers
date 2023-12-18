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
        Schema::create('book_a_trucks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('time');
            $table->string('date');
            $table->string('pickup_address');
            $table->string('dropoff_address');
            $table->double('pickup_latitude');
            $table->double('dropoff_latitude');
            $table->double('pickup_longitude');
            $table->double('dropoff_longitude');
            $table->enum('truck_type',['Small','Medium', 'Large']);
            $table->json('goods_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('book_a_trucks',function(Blueprint $table){
            $table->dropForeign(['user_id']);
        });
        Schema::dropIfExists('book_a_trucks');
    }
};
