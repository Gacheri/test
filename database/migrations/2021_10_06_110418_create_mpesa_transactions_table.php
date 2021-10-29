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
        Schema::create('mpesa_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('TransactionType')->default('donate');
            $table->string('TransID')->default('donate');
            $table->string('TransTime')->default('donate');
            $table->decimal('TransAmount', 8,2)->default('1');
            $table->string('BusinessShortCode')->default('donate');
            $table->string('BillRefNumber')->default('donate');
            $table->string('InvoiceNumber')->default('donate');
            $table->decimal('OrgAccountBalance',8,2)->default('1');
            $table->string('ThirdPartyTransID')->default('donate');
            $table->string('MSISDN')->default('donate');
            $table->string('FirstName')->default('Brenda');
            $table->string('MiddleName')->default('Gaceri');
            $table->string('LastName')->default('donate');
            $table->text('response')->default('response');
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
        Schema::dropIfExists('mpesa_transactions');
    }

};
