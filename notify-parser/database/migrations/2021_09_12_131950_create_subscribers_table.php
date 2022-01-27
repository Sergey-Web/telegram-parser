<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscribersTable extends Migration
{
    public function up(): void
    {
        Schema::create('subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->boolean('status')->default(true);
            $table->timestamp('created_at', 0)->useCurrent();
            $table->timestamp('updated_at', 0)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscribers');
    }
}
