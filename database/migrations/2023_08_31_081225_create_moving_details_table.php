<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('moving_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('pickup_address')->nullable();
            $table->string('dropoff_address')->nullable();

            $table->string('pickup_date')->nullable();
            $table->string('pickup_time')->nullable();
            $table->json('item_pictures');
            $table->text('detailed_description')->nullable();
            $table->enum('pickup_property_type', ['apartment', 'condominium', 'house','semi detached house','detached house','town house condo','stacked town house','condo town house','open basement','close basement','villa','duplex','townhouse','farmhouse']);
            $table->enum('dropoff_property_type',['apartment', 'condominium', 'house','semi detached house','detached house','town house condo','stacked town house','condo town house','open basement','close basement','villa','duplex','townhouse','farmhouse']);
            $table->string('pickup_unit_number')->nullable();
            $table->string('dropoff_unit_number')->nullable();
            $table->integer('pickup_bedrooms')->nullable();
            $table->boolean('pickup_elevator')->default(false);
            $table->integer('pickup_flight_of_stairs')->nullable();
            $table->string('pickup_elevator_timing_from')->nullable();
            $table->string('pickup_elevator_timing_to')->nullable();
            $table->boolean('dropoff_elevator')->default(false);
            $table->integer('dropoff_flight_of_stairs')->nullable();
            $table->string('dropoff_elevator_timing_from')->nullable();
            $table->string('dropoff_elevator_timing_to')->nullable();
            $table->enum('status', ['cancelled','approved','pending']);
            // Add columns for pickup and dropoff latitude and longitude
            $table->decimal('pickup_latitude', 10, 8)->nullable();
            $table->decimal('pickup_longitude', 11, 8)->nullable();
            $table->decimal('dropoff_latitude', 10, 8)->nullable();
            $table->decimal('dropoff_longitude', 11, 8)->nullable();

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
        Schema::table('moving_details', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['user_id']);
        });
        Schema::dropIfExists('moving_details');
    }
};
