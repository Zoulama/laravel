<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHivesTable extends Migration
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
         * - `visite_le` date NOT NULL,
         * - `nb_visite` int(11) NOT NULL,
         * - `sig` int(11) NOT NULL, gprs à la place
         * - `sig_gps` int(11) NOT NULL,
         * - `bat` int(11) NOT NULL, batterie
         */
        Schema::create('hives', function(Blueprint $table) {
            $table->increments('id');
            $table->string('alias')->nullable();
            $table->string('reference')->unique();
            $table->string('imei')->unique();
            $table->date('installed_at')->nullable();
            $table->string('compass')->nullable(); # se, ne.. ?
            $table->float('latitude', 10, 6)->nullable();
            $table->float('longitude', 10, 6)->nullable();
            $table->float('altitude', 10, 6)->nullable();
            $table->string('phone_number', 32)->nullable();
            $table->string('pin_code', 16)->nullable();
            $table->string('puk_code', 16)->nullable();
            $table->text('comment')->nullable();

            // $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hives');
    }
}
