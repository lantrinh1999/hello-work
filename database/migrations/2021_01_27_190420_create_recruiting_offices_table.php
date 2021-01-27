<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecruitingOfficesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recruiting_offices', function (Blueprint $table) {
            $table->id();
            $table->string('office_number')->nullable();
            $table->string('office_name')->nullable();
            $table->string('location')->nullable();
            $table->string('home_page')->nullable();
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
        Schema::dropIfExists('recruiting_offices');
    }
}
