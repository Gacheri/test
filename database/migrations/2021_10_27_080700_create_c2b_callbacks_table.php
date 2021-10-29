<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('c2b_callbacks', function (Blueprint $table) {
            $table->id();
            $table->string('MerchantRequestID');
            $table->string('CheckoutRequestID');
            $table->integer('ResultCode');
            $table->string('ResultDesc');
            $table->decimal('transAmount', 8,2);
            $table->string('MpesaReceiptNumber');
            $table->string('TransactionDate');
            $table->string('PhoneNumber');
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
        Schema::dropIfExists('c2b_callbacks');
    }
};
