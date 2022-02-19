<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConvertingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("convertings", function (Blueprint $table) {
            $table->id();
            $table->string("currency_from", 3)->nullable(false);
            $table->string("currency_to", 3)->nullable(false);
            $table->double("value" )->nullable(false);
            $table->double("converted_value" )->nullable(false);
            $table->double("rate" )->nullable(false);

            $table->timestamp("created_at")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('convertings');
    }
}
