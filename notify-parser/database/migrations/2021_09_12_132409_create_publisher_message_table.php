<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePublisherMessageTable extends Migration
{
    public function up(): void
    {
        Schema::create('publisher_message', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('publisher_id');
            $table->unsignedBigInteger('message_id');

            $table->foreign('publisher_id')
                ->references('id')
                ->on('publishers')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            ;
            $table->foreign('message_id')
                ->references('id')
                ->on('messages')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            ;

            $table->unique(['publisher_id', 'message_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publisher_message');
    }
}
