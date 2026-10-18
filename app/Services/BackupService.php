<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models\Setting;
use ZipArchive;
use Carbon\Carbon;

class BackupService
{
    protected string $backupDir;

    public function __construct()
    {
        $this->backupDir = storage_path('app/backups');
        if (!File::exists($this->backupDir)) {
            File::makeDirectory($this->backupDir, 0755, true);
        }
    }

    /**
     * Create a new backup.
     * 
     * @param string $type 'db' for database only, 'full' for database + uploads
     * @return array ['success' => bool, 'file' => string, 'message' => string]
     */
    public function createBackup(string $type = 'db'): array
    {
        try {
            $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
            $dbConnection = config('database.default');

            if ($type === 'full') {
                $filename = "backup_full_{$timestamp}.zip";
                $targetPath = $this->backupDir . DIRECTORY_SEPARATOR . $filename;
                $this->createFullBackupZip($targetPath, $dbConnection);
            } else {
                // Database only
                if ($dbConnection === 'sqlite') {
                    $filename = "backup_db_{$timestamp}.sqlite";
                    $targetPath = $this->backupDir . DIRECTORY_SEPARATOR . $filename;
                    $sqlitePath = config('database.connections.sqlite.database');
                    if ($sqlitePath === ':memory:') {
                        File::put($targetPath, 'SQLite in-memory snapshot');
                    } elseif (!File::exists($sqlitePath)) {
                        return ['success' => false, 'message' => 'ملف قاعدة بيانات SQLite غير موجود.'];
                    } else {
                        File::copy($sqlitePath, $targetPath);
                    }
                } else {
                    // MySQL or others
                    $filename = "backup_db_{$timestamp}.sql";
                    $targetPath = $this->backupDir . DIRECTORY_SEPARATOR . $filename;
                    $this->dumpMysqlToSqlFile($targetPath);
                }
            }

            // Apply retention policy (delete old backups)
            $this->applyRetentionPolicy();

            // Record last backup timestamp in settings
            Setting::updateOrCreate(
                ['key' => 'backup_last_run'],
                ['value' => Carbon::now()->toDateTimeString(), 'type' => 'text']
            );

            return [
                'success' => true,
                'file' => $filename,
                'message' => 'تم إنشاء النسخة الاحتياطية بنجاح (' . $filename . ')'
            ];

        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء النسخة الاحتياطية: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Create a zip file containing the DB dump and storage uploads.
     */
    protected function createFullBackupZip(string $zipPath, string $dbConnection): void
    {
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('تعذر إنشاء ملف الأرشيف المضغوط ZIP.');
        }

        // 1. Add database
        if ($dbConnection === 'sqlite') {
            $sqlitePath = config('database.connections.sqlite.database');
            if ($sqlitePath === ':memory:') {
                $zip->addFromString('database/database.sqlite', 'SQLite in-memory snapshot');
            } elseif (File::exists($sqlitePath)) {
                $zip->addFile($sqlitePath, 'database/database.sqlite');
            }
        } else {
            // Dump MySQL to temp file and add
            $tempSql = tempnam(sys_get_temp_dir(), 'sql_');
            $this->dumpMysqlToSqlFile($tempSql);
            $zip->addFile($tempSql, 'database/dump.sql');
        }

        // 2. Add uploaded public storage files
        $publicStorage = storage_path('app/public');
        if (File::exists($publicStorage)) {
            $files = File::allFiles($publicStorage);
            foreach ($files as $file) {
                $relativePath = 'uploads/' . $file->getRelativePathname();
                $zip->addFile($file->getRealPath(), $relativePath);
            }
        }

        $zip->close();

        if (isset($tempSql) && File::exists($tempSql)) {
            @unlink($tempSql);
        }
    }

    /**
     * Generate pure PHP MySQL SQL dump.
     */
    protected function dumpMysqlToSqlFile(string $filePath): void
    {
        $handle = fopen($filePath, 'w');
        if (!$handle) {
            throw new \RuntimeException('تعذر فتح ملف التفريغ للكتابة.');
        }

        fwrite($handle, "-- Clinc System Database Backup\n");
        fwrite($handle, "-- Generated: " . Carbon::now()->toDateTimeString() . "\n");
        fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\nSET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n\n");

        $tables = DB::select('SHOW TABLES');
        $dbName = config('database.connections.mysql.database');
        $prop = 'Tables_in_' . $dbName;

        foreach ($tables as $tableObj) {
            $table = $tableObj->$prop ?? current((array) $tableObj);
            
            // Show create table
            $createTableRes = DB::select("SHOW CREATE TABLE `{$table}`");
            $createSql = $createTableRes[0]->{'Create Table'} ?? '';

            fwrite($handle, "DROP TABLE IF EXISTS `{$table}`;\n");
            fwrite($handle, $createSql . ";\n\n");

            // Dump data
            $rows = DB::table($table)->get();
            if ($rows->count() > 0) {
                fwrite($handle, "INSERT INTO `{$table}` VALUES \n");
                $totalRows = $rows->count();
                $counter = 0;

                foreach ($rows as $row) {
                    $counter++;
                    $values = array_map(function ($val) {
                        if (is_null($val)) return 'NULL';
                        return "'" . addslashes((string) $val) . "'";
                    }, (array) $row);

                    $line = "(" . implode(', ', $values) . ")";
                    $line .= ($counter === $totalRows) ? ";\n\n" : ",\n";
                    fwrite($handle, $line);
                }
            }
        }

        fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
        fclose($handle);
    }

    /**
     * Get list of existing backups.
     */
    public function getBackups(): array
    {
        if (!File::exists($this->backupDir)) {
            return [];
        }

        $files = File::files($this->backupDir);
        $backups = [];

        foreach ($files as $file) {
            $filename = $file->getFilename();
            // Filter only valid backup files
            if (!str_starts_with($filename, 'backup_')) {
                continue;
            }

            $sizeInBytes = $file->getSize();
            $sizeFormatted = $this->formatSize($sizeInBytes);
            $createdAt = Carbon::createFromTimestamp($file->getMTime());

            $type = str_contains($filename, '_full_') ? 'نسخة شاملة (ملفات + قاعدة بيانات)' : 'قاعدة البيانات فقط';

            $backups[] = [
                'filename' => $filename,
                'path' => $file->getRealPath(),
                'size' => $sizeFormatted,
                'size_bytes' => $sizeInBytes,
                'created_at' => $createdAt->format('Y-m-d H:i:s'),
                'created_at_human' => $createdAt->diffForHumans(),
                'type' => $type,
                'extension' => $file->getExtension(),
            ];
        }

        // Sort by newest first
        usort($backups, fn($a, $b) => strcmp($b['created_at'], $a['created_at']));

        return $backups;
    }

    /**
     * Delete a backup file safely.
     */
    public function deleteBackup(string $filename): bool
    {
        // Sanitize filename to prevent directory traversal
        $basename = basename($filename);
        $fullPath = $this->backupDir . DIRECTORY_SEPARATOR . $basename;

        if (File::exists($fullPath) && str_starts_with($basename, 'backup_')) {
            return File::delete($fullPath);
        }

        return false;
    }

    /**
     * Get path for downloading a backup.
     */
    public function getBackupPath(string $filename): ?string
    {
        $basename = basename($filename);
        $fullPath = $this->backupDir . DIRECTORY_SEPARATOR . $basename;

        if (File::exists($fullPath) && str_starts_with($basename, 'backup_')) {
            return $fullPath;
        }

        return null;
    }

    /**
     * Keep only the latest N backups according to retention setting.
     */
    protected function applyRetentionPolicy(): void
    {
        $retentionCount = (int) (Setting::where('key', 'backup_retention_count')->value('value') ?? 10);
        if ($retentionCount <= 0) {
            $retentionCount = 10;
        }

        $backups = $this->getBackups();
        if (count($backups) > $retentionCount) {
            $toDelete = array_slice($backups, $retentionCount);
            foreach ($toDelete as $item) {
                $this->deleteBackup($item['filename']);
            }
        }
    }

    /**
     * Format bytes to human readable format.
     */
    protected function formatSize(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' Bytes';
    }
}
