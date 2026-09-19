<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Remove the retired public content modules from an existing deployment.
     *
     * The historical create migrations remain intact because they may already
     * be recorded in production's migrations table. This migration is the
     * explicit, one-way data removal step for the retired tables.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        try {
            foreach (['news', 'galleries', 'contacts'] as $table) {
                if (Schema::hasTable($table)) {
                    Schema::drop($table);
                }
            }
        } finally {
            Schema::enableForeignKeyConstraints();
        }
    }

    /**
     * This migration is intentionally irreversible because its purpose is to
     * permanently remove retired feature data.
     */
    public function down(): void
    {
        throw new \RuntimeException('The retired news, gallery, and aspiration tables cannot be restored automatically.');
    }
};
