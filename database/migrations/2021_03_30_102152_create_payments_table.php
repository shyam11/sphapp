<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('doctor_id');
            $table->integer('patient_id');
            $table->string('bill_type')->nullable()->default(null);
            $table->json('service_name')->nullable()->default(null);
            $table->string('payment_type')->nullable()->default(null);
            $table->string('uhid')->nullable()->default(null);
            $table->string('ipo')->nullable()->default(null);
            $table->timestamp('admit_date')->nullable()->default(null);
            $table->string('payment_status')->nullable()->default(null);
            $table->decimal('total_amount', 8, 2)->nullable()->default(null);
            $table->decimal('paid_amount', 8, 2)->nullable()->default(null);
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
        Schema::dropIfExists('payments');
    }
}
