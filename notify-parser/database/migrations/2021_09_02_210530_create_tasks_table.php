<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique()->comment('Task name');
            $table->string('search_text', 255);
            $table->string('search_type', 20);
            $table->boolean('status')->default(true)->comment('Task activity');
            $table->timestamp('created_at', 0)->useCurrent();
            $table->timestamp('updated_at', 0)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
}
