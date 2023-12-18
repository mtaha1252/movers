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
        Schema::create('book_a_movers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('time');
            $table->string('date');
            $table->string('loading_address')->nullable();
            $table->string('uploading_address')->nullable();
            $table->double('loading_latitude')->nullable();
            $table->double('loading_longitude')->nullable();
            $table->double('uploading_latitude')->nullable();
            $table->double('uploading_longitude')->nullable();
            $table->json('items_types');
            $table->json('pictures')->nullable();
            $table->integer('total_movers');
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
        Schema::table('book_a_movers', function(Blueprint $table){
            $table->dropForeign('user_id');
        });
        Schema::dropIfExists('book_a_movers');
    }
};
