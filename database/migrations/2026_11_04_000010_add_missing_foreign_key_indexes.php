<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración que añade índices para claves foráneas que carecían de índice propio,
 * optimizando el rendimiento de consultas y eliminaciones en cascada en PostgreSQL.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('correction_requests', function (Blueprint $table) {
            $table->index('reviewed_by', 'idx_correction_requests_reviewed_by');
        });

        Schema::table('content_suggestions', function (Blueprint $table) {
            $table->index('related_page_id', 'idx_content_suggestions_related_page_id');
        });

        Schema::table('proverbs', function (Blueprint $table) {
            $table->index('related_page_id', 'idx_proverbs_related_page_id');
        });

        Schema::table('news', function (Blueprint $table) {
            $table->index('user_id', 'idx_news_user_id');
        });

        Schema::table('category_news', function (Blueprint $table) {
            $table->index('news_id', 'idx_category_news_news_id');
        });

        Schema::table('news_page', function (Blueprint $table) {
            $table->index('page_id', 'idx_news_page_page_id');
        });
    }

    public function down(): void
    {
        Schema::table('news_page', function (Blueprint $table) {
            $table->dropIndex('idx_news_page_page_id');
        });

        Schema::table('category_news', function (Blueprint $table) {
            $table->dropIndex('idx_category_news_news_id');
        });

        Schema::table('news', function (Blueprint $table) {
            $table->dropIndex('idx_news_user_id');
        });

        Schema::table('proverbs', function (Blueprint $table) {
            $table->dropIndex('idx_proverbs_related_page_id');
        });

        Schema::table('content_suggestions', function (Blueprint $table) {
            $table->dropIndex('idx_content_suggestions_related_page_id');
        });

        Schema::table('correction_requests', function (Blueprint $table) {
            $table->dropIndex('idx_correction_requests_reviewed_by');
        });
    }
};
