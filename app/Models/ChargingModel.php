<?php

namespace App\Models;

use CodeIgniter\Model;
use Throwable;

class ChargingModel extends Model
{
    protected $table = 'z_amp_chargingA';

    public string $lastSource = 'live'; // 'live', 'preview_fallback', 'live_waiting', 'demo'
    public ?string $lastMessage = null;
    public bool $isDbConnected = false;

    /**
     * Ambil data charging berdasarkan sirkuit, interval, filter tanggal, dan line (A / F).
     * Dilengkapi fitur Smart Auto-Fallback: otomatis menggunakan data simulasi jika DB offline/belum ada data,
     * dan otomatis beralih ke data live secara realtime begitu database SQL Server terhubung.
     */
    public function getChargingData(
        string $circuit = 'Circuit 21',
        string $intervalStr = 'raw',
        ?string $startStr = null,
        ?string $endStr = null,
        bool $isDemo = false,
        string $line = 'A'
    ): array {
        $lineClean = (strtoupper(trim($line)) === 'F') ? 'F' : 'A';
        $tableName = ($lineClean === 'F') ? 'z_amp_chargingF' : 'z_amp_chargingA';

        if ($isDemo) {
            $this->lastSource = 'demo';
            $this->lastMessage = 'Mode Simulasi Manual';
            $this->isDbConnected = false;
            return $this->generateDemoData($intervalStr, $startStr, $endStr, $lineClean);
        }

        $isHistorical = !empty($startStr) && !empty($endStr);

        $intervalMinutes = 1;
        $isRaw = ($intervalStr === 'raw');
        if ($intervalStr === '10m') {
            $intervalMinutes = 10;
        } elseif ($intervalStr === '30m') {
            $intervalMinutes = 30;
        } elseif ($intervalStr === '1h') {
            $intervalMinutes = 60;
        }

        try {
            $db = \Config\Database::connect();

            if (!empty($startStr)) {
                $startStr = date('Y-m-d H:i:s', strtotime($startStr));
            }
            if (!empty($endStr)) {
                $endStr = date('Y-m-d H:i:s', strtotime($endStr));
            }

            if ($isHistorical) {
                if ($isRaw) {
                    $sql = "
                        SELECT 
                            [created_at] as [time],
                            CAST([amp] as FLOAT) as [amp],
                            CAST([voltase] as FLOAT) as [voltase]
                        FROM [prod_control].[dbo].[{$tableName}] WITH (NOLOCK)
                        WHERE [id] = ?
                          AND [created_at] >= ? 
                          AND [created_at] <= ?
                        ORDER BY [created_at] ASC
                    ";
                    $query = $db->query($sql, [$circuit, $startStr, $endStr]);
                } else {
                    $sql = "
                        SELECT 
                            DATEADD(minute, (DATEDIFF(minute, 0, [created_at]) / {$intervalMinutes}) * {$intervalMinutes}, 0) as [time],
                            AVG(CAST([amp] as FLOAT)) as [amp],
                            AVG(CAST([voltase] as FLOAT)) as [voltase]
                        FROM [prod_control].[dbo].[{$tableName}] WITH (NOLOCK)
                        WHERE [id] = ?
                          AND [created_at] >= ? 
                          AND [created_at] <= ?
                        GROUP BY DATEADD(minute, (DATEDIFF(minute, 0, [created_at]) / {$intervalMinutes}) * {$intervalMinutes}, 0)
                        ORDER BY [time] ASC
                    ";
                    $query = $db->query($sql, [$circuit, $startStr, $endStr]);
                }

                $rows = $query->getResultArray();
                if (!empty($rows)) {
                    $this->lastSource = 'live';
                    $this->lastMessage = "Live SQL Server Connected ({$tableName})";
                    $this->isDbConnected = true;
                    return $this->formatRows($rows);
                }

                $this->lastSource = 'live_empty';
                $this->lastMessage = "Tidak ada data pada rentang waktu tersebut di tabel {$tableName}";
                $this->isDbConnected = true;
                return [];
            }

            // Mode LIVE: WITH (NOLOCK) mencegah blocking saat mesin/logger sedang INSERT data
            if ($isRaw) {
                $sql = "
                    SELECT TOP 60
                        [created_at] as [time],
                        CAST([amp] as FLOAT) as [amp],
                        CAST([voltase] as FLOAT) as [voltase]
                    FROM [prod_control].[dbo].[{$tableName}] WITH (NOLOCK)
                    WHERE [id] = ?
                    ORDER BY [created_at] DESC
                ";
                $query = $db->query($sql, [$circuit]);
            } else {
                $sql = "
                    SELECT TOP 60
                        DATEADD(minute, (DATEDIFF(minute, 0, [created_at]) / {$intervalMinutes}) * {$intervalMinutes}, 0) as [time],
                        AVG(CAST([amp] as FLOAT)) as [amp],
                        AVG(CAST([voltase] as FLOAT)) as [voltase]
                    FROM [prod_control].[dbo].[{$tableName}] WITH (NOLOCK)
                    WHERE [id] = ?
                    GROUP BY DATEADD(minute, (DATEDIFF(minute, 0, [created_at]) / {$intervalMinutes}) * {$intervalMinutes}, 0)
                    ORDER BY [time] DESC
                ";
                $query = $db->query($sql, [$circuit]);
            }

            $rows = $query->getResultArray();
            if (!empty($rows)) {
                $rows = array_reverse($rows);
                $this->lastSource = 'live';
                $this->lastMessage = "Live SQL Server Connected ({$tableName})";
                $this->isDbConnected = true;
                return $this->formatRows($rows);
            }

            // Database terhubung tapi tabel belum ada data (0 rows)
            $this->lastSource = 'live_waiting';
            $this->lastMessage = "Database terhubung, menunggu data masuk ke tabel {$tableName}";
            $this->isDbConnected = true;
            return $this->generateDemoData($intervalStr, $startStr, $endStr, $lineClean);

        } catch (Throwable $e) {
            // SQL Server offline / LAN pabrik belum terhubung: fallback halus tanpa error
            log_message('notice', "Database standby in ChargingModel ({$tableName}): " . $e->getMessage());
            $this->lastSource = 'preview_fallback';
            $this->lastMessage = "Mode Siap Integrasi: Menunggu koneksi SQL Server ({$tableName})";
            $this->isDbConnected = false;

            return $this->generateDemoData($intervalStr, $startStr, $endStr, $lineClean);
        }
    }

    /**
     * Format data sesuai format index.php dan api.php
     */
    private function formatRows(array $rows): array
    {
        $chartData = [];
        foreach ($rows as $row) {
            $timeVal = $row['time'];
            if (is_object($timeVal) && method_exists($timeVal, 'format')) {
                $timeStr = $timeVal->format('Y-m-d H:i:s');
            } else {
                $timeStr = date('Y-m-d H:i:s', strtotime((string)$timeVal));
            }

            $chartData[] = [
                'time'    => $timeStr,
                'amp'     => floor(((float) ($row['amp'] ?? 0)) / 10) / 10,
                'voltase' => floor(((float) ($row['voltase'] ?? 0)) / 10) / 10,
            ];
        }
        return $chartData;
    }

    /**
     * Anti-Pattern N+1 Query Prevention:
     * Mengambil data status terakhir untuk banyak sirkuit (atau seluruh 110 sirkuit) sekaligus
     * dalam 1 kali eksekusi query SQL tunggal menggunakan Window Function ROW_NUMBER().
     * Menghilangkan 110 query terpisah di dalam looping PHP.
     */
    public function getCircuitsLatestBatch(array $circuitIds = [], string $line = 'A'): array
    {
        $lineClean = (strtoupper(trim($line)) === 'F') ? 'F' : 'A';
        $tableName = ($lineClean === 'F') ? 'z_amp_chargingF' : 'z_amp_chargingA';

        try {
            $db = \Config\Database::connect();
            
            $whereClause = '';
            $params = [];
            if (!empty($circuitIds)) {
                $placeholders = implode(',', array_fill(0, count($circuitIds), '?'));
                $whereClause = "WHERE [id] IN ({$placeholders})";
                $params = $circuitIds;
            }

            $sql = "
                WITH RankedLogs AS (
                    SELECT 
                        [id],
                        CAST([amp] as FLOAT) as [amp],
                        CAST([voltase] as FLOAT) as [voltase],
                        [created_at],
                        ROW_NUMBER() OVER (PARTITION BY [id] ORDER BY [created_at] DESC) as [rn]
                    FROM [prod_control].[dbo].[{$tableName}] WITH (NOLOCK)
                    {$whereClause}
                )
                SELECT [id], [amp], [voltase], [created_at] as [time]
                FROM RankedLogs
                WHERE [rn] = 1
                ORDER BY [id] ASC
            ";

            $query = $db->query($sql, $params);
            return $this->formatRows($query->getResultArray());
        } catch (Throwable $e) {
            log_message('notice', "Batch circuits standby ({$tableName}): " . $e->getMessage());
            return [];
        }
    }

    /**
     * Demo data generator saat server database offline/tidak terkoneksi di LAN
     */
    private function generateDemoData(string $intervalStr, ?string $startStr, ?string $endStr, string $line = 'A'): array
    {
        $data = [];
        $points = 40;
        $now = time();
        $step = 5; // detik
        if ($intervalStr === '10m') $step = 600;
        if ($intervalStr === '30m') $step = 1800;
        if ($intervalStr === '1h')  $step = 3600;

        $startTime = $now - ($points * $step);
        $ampOffset = ($line === 'F') ? 2.5 : 0.0;
        $voltOffset = ($line === 'F') ? 0.6 : 0.0;

        for ($i = 0; $i < $points; $i++) {
            $timePoint = $startTime + ($i * $step);
            $baseAmp = 12.5 + $ampOffset + sin($i / 4) * 2.2 + (mt_rand(-3, 3) / 10);
            $baseVolt = 14.2 + $voltOffset + cos($i / 5) * 0.7 + (mt_rand(-2, 2) / 10);
            $data[] = [
                'time'    => date('Y-m-d H:i:s', $timePoint),
                'amp'     => round(max(0, $baseAmp), 1),
                'voltase' => round(max(0, $baseVolt), 1),
            ];
        }
        return $data;
    }
}
