<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		/*
		 * Il manque :
		 * - `Ouv` int(11) NOT NULL,
		 */
		Schema::create('reports', function(Blueprint $table) {
            $table->integer('hive_id');
			$table->datetime('at');
			$table->float('noise')->nullable(); # db
			$table->float('frequency')->nullable(); # hz
			$table->float('hygrometry')->nullable(); # %
			$table->float('battery_level')->nullable(); # %
			# temperatures °C
			$table->float('temperature_1')->nullable();
			$table->float('temperature_2')->nullable();
			$table->float('temperature_3')->nullable();
			$table->float('temperature_4')->nullable();
			$table->float('temperature_cpu')->nullable();
			# weights kg
			$table->float('weight_1')->nullable();
			$table->float('weight_2')->nullable();
			$table->float('weight_3')->nullable();
			$table->float('weight_4')->nullable();
			$table->float('weight_5')->nullable();
			$table->float('weight_6')->nullable();
			$table->float('weight_7')->nullable();
			$table->float('weight_8')->nullable();

            // $table->primary(['hive_id', 'at']);
            // $table->foreign('hive_id')->references('id')->on('hives');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('reports');
	}
}
