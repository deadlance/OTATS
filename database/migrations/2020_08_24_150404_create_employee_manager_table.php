<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeManagerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_manager', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_user_id');
            $table->unsignedBigInteger('manager_user_id');
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
        Schema::table('employee_manager', function (Blueprint $table) {
            Schema::dropIfExists('employee_manager');
        });
    }
}
