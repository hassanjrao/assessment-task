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
        // Replace me with a brief explanation of why floats aren't the correct data type, and replace with the correct data type.
        Schema::create('affiliates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('merchant_id');
            // TODO: Replace me with a brief explanation of why floats aren't the correct data type, and replace with the correct data type.
            //$table->float('commission_rate');

            //Floating-point numbers can have rounding errors, and when dealing with financial calculations, it's crucial to maintain precision to avoid discrepancies.
            // therefore it is recommended to use the decimal data type. The decimal data type provides fixed-point arithmetic, which helps avoid rounding errors associated with floating-point numbers.
            $table->decimal('commission_rate',10,3);

            $table->string('discount_code');
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
        Schema::dropIfExists('affiliates');
    }
};
