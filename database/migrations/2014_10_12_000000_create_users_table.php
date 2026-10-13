<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id'); //Primary Key, AUTO_INCREMENT
            $table->string('full_name', 255);
            $table->string('email', 255)->unique();
            $table->string('password', 255);

            $table->enum('role', ['Super Admin', 'Admin', 'Authentication User']);

            $table->string('department', 255);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
