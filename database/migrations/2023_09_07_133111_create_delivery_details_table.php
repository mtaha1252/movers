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
        Schema::create('delivery_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->json('pickup_address')->nullable();
            $table->json('dropoff_address')->nullable();
            $table->string('pickup_date')->nullable();
            $table->string('pickup_time')->nullable();
            $table->text('detailed_description')->nullable();
            $table->string('number_of_items')->nullable();
            $table->boolean('heavey_weight_items')->default(false);
            $table->enum('pickup_property_type', ['apartment', 'condominium', 'house','semi detached house','detached house','town house condo','stacked town house','condo town house','open basement','close basement','villa','duplex','townhouse','farmhouse']);
            $table->string('pickup_unit_number')->nullable();
            $table->string('pickup_flight_of_stairs')->nullable();
            $table->boolean('pickup_elevator')->default(false);
            $table->text('pickup_elevator_timing_from')->nullable();
            $table->text('pickup_elevator_timing_to')->nullable();
            $table->enum('dropoff_property_type', ['apartment', 'condominium', 'house','semi detached house','detached house','town house condo','stacked town house','condo town house','open basement','close basement','villa','duplex','townhouse','farmhouse']);
            $table->integer('dropoff_unit_number')->nullable();
            $table->boolean('dropoff_elevator')->default(false);
            $table->text('dropoff_elevator_timing_from')->nullable();
            $table->text('dropoff_elevator_timing_to')->nullable();
            $table->string('dropoff_flight_of_stairs')->nullable();
            $table->json('pickup1_pictures')->nullable();
            $table->json('pickup2_pictures')->nullable();
            $table->json('pickup3_pictures')->nullable();
            // Add columns for pickup and dropoff latitude and longitude
            $table->json('pickup_latitude')->nullable();
            $table->json('pickup_longitude')->nullable();
            $table->json('dropoff_latitude')->nullable();
            $table->json('dropoff_longitude')->nullable();

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
        Schema::dropIfExists('delivery_details');
    }
};
