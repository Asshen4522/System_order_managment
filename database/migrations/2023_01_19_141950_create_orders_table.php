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
            $table->string('city')->nullable(true);
            $table->string('firm')->nullable(true);
            $table->integer('budget')->nullable(true);
            $table->integer('daily_cost')->nullable(true);
            $table->string('housing')->nullable(true);
            $table->integer('rent')->nullable(true);
            $table->integer('tangen')->nullable(true);
            $table->integer('cup')->nullable(true);
            $table->integer('payment')->nullable(true);
            $table->foreignId('contact_id')->nullable(true)->constrained('contact_people');
            $table->date('created_at')->nullable(true);
            $table->date('date_end')->nullable(true);
            $table->foreignId('executor_id')->nullable(true)->constrained('users')->nullable(true);
            $table->foreignId('status_id')->constrained('statuses');
        });
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
