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
        Schema::create('ticket_replies', function (Blueprint $table) {
            $table->id('reply_id');
            $table->unsignedBigInteger('ticket_id');
            $table->unsignedBigInteger('user_id')->nullable(); // ID pengirim
            $table->string('sender_name');
            $table->string('sender_role')->default('Admin'); // 'Admin' atau 'User'
            $table->text('message');
            $table->string('attachment')->nullable();
            $table->timestamps();

            // Foreign key ke jadual tickets
            $table->foreign('ticket_id')->references('ticket_id')->on('tickets')->onDelete('cascade');
        });
    }
};