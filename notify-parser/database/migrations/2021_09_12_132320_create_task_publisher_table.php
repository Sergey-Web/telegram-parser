<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskPublisherTable extends Migration
{
    public function up(): void
    {
        Schema::create('task_publisher', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id');
            $table->unsignedBigInteger('publisher_id');

            $table->foreign('task_id')
                ->references('id')
                ->on('tasks')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            ;
            $table->foreign('publisher_id')
                ->references('id')
                ->on('publishers')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            ;

            $table->unique(['task_id', 'publisher_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_publisher');
    }
}
