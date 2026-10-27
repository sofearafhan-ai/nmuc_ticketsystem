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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id('item_id'); //Primary Key, AUTO_INCREMENT

            $table->string('item_name', 255);
            $table->string('item_category', 255);
            $table->string('item_model', 255);

            $table->enum('item_status', ['Active', 'Inactive'])->default('Active');
            $table->string('item_location', 255);

            $table->string('item_attachment', 255)->nullable();

            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');

            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
