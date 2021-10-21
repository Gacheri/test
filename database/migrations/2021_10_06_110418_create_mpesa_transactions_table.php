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
            $table->string('TransactionType');
            $table->string('TransID');
            $table->string('TransTime');
            $table->decimal('TransAmount', 8,2);
            $table->string('BusinessShortCode');
            $table->string('BillRefNumber');
            $table->string('InvoiceNumber');
            $table->decimal('OrgAccountBalance',8,2);
            $table->string('ThirdPartyTransID');
            $table->string('MSISDN');
            $table->string('FirstName');
            $table->string('MiddleName');
            $table->string('LastName');
            $table->text('response');
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
