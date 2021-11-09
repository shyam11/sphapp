<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDoctorPatientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('doctor_patients', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('doctor_id');
            $table->integer('patient_id');
            $table->timestamp('registration_date');
            $table->timestamp('valid_till');
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
        Schema::dropIfExists('doctor_patients');
    }
}
