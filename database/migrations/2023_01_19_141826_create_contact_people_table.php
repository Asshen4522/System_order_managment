<?php

use App\Models\contactPerson;
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
        Schema::create('contact_people', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('surname')->nullable(true);
            $table->string('patronymic')->nullable(true);
            $table->string('phone');
        });

        contactPerson::create([
            'name' => 'Сергей',
            'phone' => '89633854682'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contact_people');
    }
};
