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
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->json('pickup_address')->nullable();
            $table->json('dropoff_address')->nullable();
            $table->string('pickup_date')->nullable();
            $table->string('current_date')->nullable();
            $table->string('pickup_time')->nullable();
            $table->text('detailed_description')->nullable();
            $table->json('number_of_items')->nullable();
            $table->json('heavey_weight_items')->nullable();
            $table->json('pickup_property_type')->nullable(); // ['apartment', 'condominium', 'house','semi detached house','detached house','town house condo','stacked town house','condo town house','open basement','close basement','villa','duplex','townhouse','farmhouse']);
            $table->json('pickup_unit_number')->nullable();
            $table->json('pickup_flight_of_stairs')->nullable();
            $table->json('pickup_elevator')->nullable();
            $table->json('pickup_elevator_timing_from')->nullable();
            $table->json('pickup_elevator_timing_to')->nullable();
            $table->json('dropoff_property_type')->nullable(); //['apartment', 'condominium', 'house','semi detached house','detached house','town house condo','stacked town house','condo town house','open basement','close basement','villa','duplex','townhouse','farmhouse']);
            $table->json('dropoff_unit_number')->nullable();
            $table->json('dropoff_elevator')->nullanle();
            $table->json('dropoff_elevator_timing_from')->nullable();
            $table->json('dropoff_elevator_timing_to')->nullable();
            $table->json('dropoff_flight_of_stairs')->nullable();
            $table->enum('status', ['cancelled','approved','pending']);
            $table->json('pickup1_pictures')->nullable();
            $table->json('pickup2_pictures')->nullable();
            $table->json('pickup3_pictures')->nullable();
            // Add columns for pickup and dropoff latitude and longitude
            $table->json('pickup_latitude')->nullable();
            $table->json('pickup_longitude')->nullable();
            $table->json('dropoff_latitude')->nullable();
            $table->json('dropoff_longitude')->nullable();
            $table->string('total_distance_price')->nullable();
            $table->string('total_time_price')->nullable();
            $table->string('heavy_items_price')->nullable();
            $table->string('assemble_price')->nullable();
            $table->string('disassemble_price')->nullable();
            $table->string('truck_fee')->nullable();
            $table->boolean('special_delivery')->nullable();
            $table->json('reciept_image')->nullable();
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
         
        Schema::table('delivery_details', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['user_id']);
        });
        Schema::dropIfExists('delivery_details');
    }
};
