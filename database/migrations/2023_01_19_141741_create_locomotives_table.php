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
            'model' => 'ЧМЭЗ',
            'wheel_pairs' => 6
        ]);
        locomotive::create([
            'model' => 'ТГМ',
            'wheel_pairs' => 6
        ]);
        locomotive::create([
            'model' => 'ТГМ4',
            'wheel_pairs' => 6
        ]);
        locomotive::create([
            'model' => 'ТГМ6',
            'wheel_pairs' => 6
        ]);
        locomotive::create([
            'model' => 'УТМ',
            'wheel_pairs' => 6
        ]);
        locomotive::create([
            'model' => 'РШП',
            'wheel_pairs' => 6
        ]);
        locomotive::create([
            'model' => 'ТЭМ',
            'wheel_pairs' => 6
        ]);
        locomotive::create([
            'model' => 'ТЭМ2',
            'wheel_pairs' => 6
        ]);
        locomotive::create([
            'model' => 'ТЭМ9',
            'wheel_pairs' => 6
        ]);
        locomotive::create([
            'model' => 'ТЭМ15',
            'wheel_pairs' => 6
        ]);
        locomotive::create([
            'model' => 'АДМ СКМ',
            'wheel_pairs' => 6
        ]);
        locomotive::create([
            'model' => 'ТТЭМ7',
            'wheel_pairs' => 6
        ]);
        locomotive::create([
            'model' => 'ТЭ116',
            'wheel_pairs' => 6
        ]);
        locomotive::create([
            'model' => 'ТГМ23',
            'wheel_pairs' => 6
        ]);
        locomotive::create([
            'model' => 'ТГМ40',
            'wheel_pairs' => 6
        ]);
        locomotive::create([
            'model' => 'ПТМ',
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
