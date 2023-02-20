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
        Schema::table('orders', function (Blueprint $table) {


            $table->integer('budget')->nullable(true)->change();
            $table->integer('daily_cost')->nullable(true)->change();
            $table->string('housing')->nullable(true)->change();
            $table->integer('rent')->nullable(true)->change();
            $table->integer('tangen')->nullable(true)->change();
            $table->integer('cup')->nullable(true)->change();
            $table->integer('wheel_pairs')->nullable(true)->change();
            $table->foreignId('executor_id')->nullable(true)->constrained('users');
            $table->foreignId('contact_id')->nullable(true)->constrained('contact_people');
            $table->date('created_at')->nullable(true)->change();
        });

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
        //
    }
};
