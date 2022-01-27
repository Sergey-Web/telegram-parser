<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriberSenderTable extends Migration
{
    public function up(): void
    {
        Schema::create('subscriber_sender', function (Blueprint $table) {

            $table->id();
            $table->unsignedBigInteger('subscriber_id');
            $table->unsignedBigInteger('sender_id');

            $table->foreign('subscriber_id')
                ->references('id')
                ->on('subscribers')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreign('sender_id')
                ->references('id')
                ->on('senders')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->unique(['subscriber_id', 'sender_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriber_sender');
    }
}
