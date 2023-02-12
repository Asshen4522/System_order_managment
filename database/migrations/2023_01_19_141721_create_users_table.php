<?php

use App\Models\user;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('surname');
            $table->string('phone');
            $table->foreignId('role_id')->constrained('roles');
            $table->string('password');
        });

        user::create([
            'name' => 'Иван',
            'surname' => 'Плотников',
            'phone' => '89161343606',
            'role_id' => 1,
            'password' => Hash::make('plotnikov')
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
