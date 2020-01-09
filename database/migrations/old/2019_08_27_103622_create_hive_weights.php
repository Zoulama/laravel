<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHiveWeights extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hive_weights', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('weight_reference_id')->nullable($value = true);
            $table->int('bottom_board')->nullable($value = true);
            $table->int('body')->nullable($value = true);
            $table->int('body_frames')->nullable($value = true);
            $table->int('body_waxed_frames')->nullable($value = true);
            $table->int('super')->nullable($value = true);
            $table->int('super_frames')->nullable($value = true);
            $table->int('super_waxed_frames')->nullable($value = true);
            $table->int('inner_cover')->nullable($value = true);
            $table->int('wooden_flat_cover')->nullable($value = true);
            $table->int('wooden_garden_cover')->nullable($value = true);
            $table->int('metal_flat_80_cover')->nullable($value = true);
            $table->int('metal_flat_105_cover')->nullable($value = true);
            $table->int('is_tare_on')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hive_weights');
        /*Schema::table('hive_weights', function (Blueprint $table) {
            //
        });*/
    }
}
