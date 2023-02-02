<?php

use App\Models\order;
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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('city');
            $table->foreignId('locomotive_id')->constrained('locomotives');
            $table->integer('budget');
            $table->integer('daily_cost');
            $table->string('housing');
            $table->integer('rent');
            $table->integer('tangen');
            $table->integer('cup');
            $table->integer('wheel_pairs');
            $table->foreignId('contact_id')->constrained('contact_people');
            $table->date('created_at');
            $table->date('date_end')->nullable(true);
            $table->foreignId('executor_id')->constrained('users')->nullable(true);
            $table->foreignId('status_id')->constrained('statuses');
        });

        order::create([
            'city' => 'Санкт-Петербург',
            'locomotive_id' => 2,
            'budget' => 250000,
            'daily_cost' => 1000,
            'housing' => 'Руставелли д 7 кв 15',
            'rent' => 800,
            'tangen' => 10,
            'cup' => 10,
            'wheel_pairs' => 6,
            'contact_id' => 1,
            'created_at' => '2023-02-04',
            'executor_id' => 2,
            'status_id' => 1
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
