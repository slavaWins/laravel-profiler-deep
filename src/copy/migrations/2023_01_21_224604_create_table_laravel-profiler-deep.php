<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablePackagemaker extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('virtual_steps', function (Blueprint $table) {
            $table->string("selector_character_filter")->nullable();
            $table->string("selector_character_to_varible")->nullable();
        });

        Schema::table('virtual_rooms', function (Blueprint $table) {
            //$table->string("selector_character")->nullable();
        });

        Schema::table('scenes', function (Blueprint $table) {
            $table->boolean("debug_play")->nullable()->comment("Комната запущена в редактора, потом должна удалится");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('virtual_steps', function (Blueprint $table) {
           $table->dropColumn(['selector_character_filter','selector_character_to_varible']);
        });
        Schema::table('virtual_rooms', function (Blueprint $table) {
            $table->dropColumn(['player_id']);
        });
        Schema::table('scenes', function (Blueprint $table) {
            $table->dropColumn(['debug_play']);
        });
    }
}
