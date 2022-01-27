<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskSubscriberTable extends Migration
{
    public function up(): void
    {
        Schema::create('task_subscriber', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id');
            $table->unsignedBigInteger('subscriber_id');

            $table->foreign('task_id')
                ->references('id')
                ->on('tasks')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            ;
            $table->foreign('subscriber_id')
                ->references('id')
                ->on('subscribers')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            ;

            $table->unique(['task_id', 'subscriber_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_subscriber');
    }
}
