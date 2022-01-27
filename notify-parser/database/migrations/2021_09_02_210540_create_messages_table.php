<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->string('channel_id', 255);
            $table->text('message')->comment('Publication');
            $table->bigInteger('user_id')->nullable(true);
            $table->string('user_name', 255)->nullable(true);
            $table->string('user_last_name', 255)->nullable(true);
            $table->bigInteger('public_id')->comment('Telegram id message');
            $table->bigInteger('public_date')->comment('Telegram date message');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at', 0)->nullable();
            $table->unique(['public_id', 'channel_id']);
        });

        DB::statement("ALTER TABLE messages ADD COLUMN search_text TSVECTOR");
        DB::statement("UPDATE messages SET search_text = to_tsvector('russian', coalesce(message,''))");
        DB::statement('CREATE EXTENSION IF NOT EXISTS pg_trgm');
        DB::statement("CREATE INDEX search_text_gin ON messages USING GIN(search_text)");
        DB::statement("CREATE TRIGGER ts_search_text BEFORE INSERT OR UPDATE ON messages FOR EACH ROW EXECUTE PROCEDURE tsvector_update_trigger('search_text', 'pg_catalog.russian', 'message')");
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
}
