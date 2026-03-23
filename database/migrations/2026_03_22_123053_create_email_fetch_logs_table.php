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
        Schema::create('email_fetch_logs', function (Blueprint $table) {
            $table->id();
            
            // Информация о письме
            $table->string('message_id')->index();
            $table->string('from_email');
            $table->string('from_name')->nullable();
            $table->string('subject');
            $table->timestamp('email_date');
            
            // Результат обработки
            $table->enum('status', ['success', 'failed', 'skipped']);
            $table->foreignId('post_id')->nullable()->constrained('posts')->onDelete('set null');
            $table->text('error_message')->nullable();
            
            // Статистика
            $table->integer('attachments_count')->default(0);
            $table->bigInteger('total_size')->default(0); // в байтах
            
            // Мета
            $table->ipAddress('processed_from')->nullable(); // IP сервера
            $table->json('metadata')->nullable(); // Дополнительные данные
            
            $table->timestamps();
            
            // Индексы для быстрого поиска
            $table->index('status');
            $table->index('created_at');
            $table->index(['from_email', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_fetch_logs');
    }
};
