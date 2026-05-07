<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    /**
     * Привести таблицу game_files к защищенной архитектуре хранения.
     */
    public function up(): void
    {
        Schema::table('game_files', function (Blueprint $table) {
            if (!Schema::hasColumn('game_files', 'uploaded_by')) {
                $table->foreignId('uploaded_by')
                    ->nullable()
                    ->after('game_id')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('game_files', 'original_file_name')) {
                $table->string('original_file_name')->nullable()->after('file_name');
            }

            if (!Schema::hasColumn('game_files', 'storage_disk')) {
                $table->string('storage_disk', 50)->default('local_private')->after('original_file_name');
            }

            if (!Schema::hasColumn('game_files', 'storage_path')) {
                $table->string('storage_path')->nullable()->after('storage_disk');
            }

            if (!Schema::hasColumn('game_files', 'mime_type')) {
                $table->string('mime_type')->nullable()->after('file_size');
            }

            if (!Schema::hasColumn('game_files', 'md5_hash')) {
                $table->string('md5_hash', 32)->nullable()->after('version');
            }

            if (!Schema::hasColumn('game_files', 'download_count')) {
                $table->unsignedBigInteger('download_count')->default(0)->after('md5_hash');
            }

            if (!Schema::hasColumn('game_files', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('download_count');
            }
        });

        if (Schema::hasColumn('game_files', 'file_path') && Schema::hasColumn('game_files', 'storage_path')) {
            DB::statement("UPDATE game_files SET storage_path = file_path WHERE storage_path IS NULL OR storage_path = ''");
        }

        if (Schema::hasColumn('game_files', 'file_name') && Schema::hasColumn('game_files', 'original_file_name')) {
            DB::statement("UPDATE game_files SET original_file_name = file_name WHERE original_file_name IS NULL OR original_file_name = ''");
        }

        if (Schema::hasColumn('game_files', 'storage_disk')) {
            DB::statement("UPDATE game_files SET storage_disk = 'local_private' WHERE storage_disk IS NULL OR storage_disk = ''");
        }

        if (Schema::hasColumn('game_files', 'uploaded_by')) {
            DB::statement('UPDATE game_files gf INNER JOIN games g ON g.id = gf.game_id SET gf.uploaded_by = g.seller_id WHERE gf.uploaded_by IS NULL');
        }

        if (Schema::hasColumn('game_files', 'mime_type')) {
            DB::statement("UPDATE game_files SET mime_type = 'application/octet-stream' WHERE mime_type IS NULL OR mime_type = ''");
        }

        if (Schema::hasColumn('game_files', 'storage_path') && Schema::hasColumn('game_files', 'md5_hash')) {
            $files = DB::table('game_files')
                ->select('id', 'storage_disk', 'storage_path')
                ->get();

            foreach ($files as $file) {
                $disk = $file->storage_disk ?: 'local_private';
                $path = $file->storage_path;

                if (!$path || !Storage::disk($disk)->exists($path)) {
                    continue;
                }

                $absolutePath = Storage::disk($disk)->path($path);
                $hash = md5_file($absolutePath) ?: null;

                DB::table('game_files')
                    ->where('id', $file->id)
                    ->update(['md5_hash' => $hash]);
            }
        }

        if (Schema::hasColumn('game_files', 'file_path')) {
            Schema::table('game_files', function (Blueprint $table) {
                $table->dropColumn('file_path');
            });
        }

        if (Schema::hasColumn('game_files', 'checksum')) {
            Schema::table('game_files', function (Blueprint $table) {
                $table->dropColumn('checksum');
            });
        }

        Schema::table('game_files', function (Blueprint $table) {
            $table->index(['game_id', 'is_active'], 'game_files_game_id_is_active_index');
            $table->index('uploaded_by', 'game_files_uploaded_by_index');
            $table->index('storage_disk', 'game_files_storage_disk_index');
        });
    }

    /**
     * Частичный откат новых полей.
     */
    public function down(): void
    {
        Schema::table('game_files', function (Blueprint $table) {
            $table->dropIndex('game_files_game_id_is_active_index');
            $table->dropIndex('game_files_uploaded_by_index');
            $table->dropIndex('game_files_storage_disk_index');

            foreach (['uploaded_by', 'original_file_name', 'storage_disk', 'storage_path', 'mime_type', 'md5_hash', 'download_count', 'is_active'] as $column) {
                if (Schema::hasColumn('game_files', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
