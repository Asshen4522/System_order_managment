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
        Schema::create('order_locomotives', function (Blueprint $table) {
            $table->foreignId('locomotive_id')->constrained('locomotives');
            $table->foreignId('order_id')->constrained('orders');
            $table->integer('amount');
            $table->integer('wheel_pairs');
            $table->integer('done')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_locomotives');
    }
};
