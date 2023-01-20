<?php

use App\Models\locomotive;
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
        Schema::create('locomotives', function (Blueprint $table) {
            $table->id();
            $table->string('model');
            $table->integer('wheel_pairs');
        });

        locomotive::create([
            'model' => 'ТЭМ',
            'wheel_pairs' => 6
        ]);

        locomotive::create([
            'model' => 'ЧМЭЗ',
            'wheel_pairs' => 6
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('locomotives');
    }
};
