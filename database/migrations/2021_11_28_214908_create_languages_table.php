<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLanguagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection(config('localization.database_connection'))->create(config('localization.table_name'), function (Blueprint $table) {
            $table->id();
            $table->boolean('status')->default(true);
            $table->string('name');
            $table->string('script')->nullable();
            $table->string('native')->nullable();
            $table->string('regional')->nullable();
            $table->string('image')->nullable();
            $table->integer('sort')->default(1);
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
        Schema::dropIfExists('languages');
    }
}
