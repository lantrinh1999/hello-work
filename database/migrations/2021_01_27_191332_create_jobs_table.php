<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('job_number')->nullable();
            $table->string('reception_date')->nullable();
            $table->string('referral_deadline')->nullable();
            $table->string('type')->comment('full-time, part-time')->nullable();
            $table->text('hope')->nullable();
            $table->bigInteger('recruiting_office_id')->unsigned()->nullable(); //
            $table->string('wage')->nullable();
            $table->string('holiday')->nullable();
            $table->timestamps();
            $table->string('occupation')->nullable();
            $table->text('job_description')->nullable();
            $table->text('contract')->nullable();
            $table->text('employment_period')->nullable();
            $table->text('work_place')->nullable();
            $table->text('station')->nullable();
            $table->text('min_salary')->nullable();
            $table->text('max_salary')->nullable();
            $table->text('private_car_commute')->nullable();
            $table->text('possibility_of_transfer')->nullable();
            $table->text('age')->nullable();
            $table->text('educational_background')->nullable();
            $table->text('required')->nullable();
            $table->text('license')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jobs');
    }
}
