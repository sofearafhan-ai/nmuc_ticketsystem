<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id('ticket_id'); // Primary Key
            
            $table->string('user_name', 255);
            $table->string('user_email', 255);
            
            $table->text('title');
            $table->text('description');
            
            $table->string('image', 255)->nullable();
            
            $table->enum('type', ['Incident', 'Request']);
            $table->string('category', 255);
            
            $table->date('date');
            
            $table->enum('status', ['New', 'Processing', 'Pending', 'Close']);
            $table->enum('priority', ['Low', 'Medium', 'High', 'Urgent']);
            
            $table->unsignedBigInteger('user_id');
            
            $table->string('location', 255);
            
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};