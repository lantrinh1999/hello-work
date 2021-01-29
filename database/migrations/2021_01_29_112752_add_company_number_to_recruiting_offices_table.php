<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCompanyNumberToRecruitingOfficesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('recruiting_offices', 'company_number')) {
            Schema::table('recruiting_offices', function (Blueprint $table) {
                $table->string('company_number')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('recruiting_offices', 'company_number')) {
            Schema::table('recruiting_offices', function (Blueprint $table) {
                $table->dropColumn('company_number');
            });
        }
    }
}
