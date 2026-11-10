<?php

namespace App\Models;

use CodeIgniter\Model;
use Throwable;

class ChargingModel extends Model
{
    protected $table = 'z_amp_chargingA';

    /**
     * Ambil data charging berdasarkan sirkuit, interval, filter tanggal, dan line (A / F).
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
                        FROM [prod_control].[dbo].[{$tableName}]
                        WHERE [id] = ?
                          AND [created_at] >= ? 
                          AND [created_at] <= ?
                        ORDER BY [time] ASC
                    ";
                    $query = $db->query($sql, [$circuit, $startStr, $endStr]);
                } else {
                    $sql = "
                        SELECT 
                            DATEADD(minute, (DATEDIFF(minute, 0, [created_at]) / {$intervalMinutes}) * {$intervalMinutes}, 0) as [time],
                            AVG(CAST([amp] as FLOAT)) as [amp],
                            AVG(CAST([voltase] as FLOAT)) as [voltase]
                        FROM [prod_control].[dbo].[{$tableName}]
                        WHERE [id] = ?
                          AND [created_at] >= ? 
                          AND [created_at] <= ?
                        GROUP BY DATEADD(minute, (DATEDIFF(minute, 0, [created_at]) / {$intervalMinutes}) * {$intervalMinutes}, 0)
                        ORDER BY [time] ASC
                    ";
                    $query = $db->query($sql, [$circuit, $startStr, $endStr]);
                }

                $rows = $query->getResultArray();
                return $this->formatRows($rows);
            }

            // Mode LIVE
            if ($isRaw) {
                $sql = "
                    SELECT TOP 60
                        [created_at] as [time],
                        CAST([amp] as FLOAT) as [amp],
                        CAST([voltase] as FLOAT) as [voltase]
                    FROM [prod_control].[dbo].[{$tableName}]
                    WHERE [id] = ?
                    ORDER BY [time] DESC
                ";
                $query = $db->query($sql, [$circuit]);
            } else {
                $sql = "
                    SELECT TOP 60
                        DATEADD(minute, (DATEDIFF(minute, 0, [created_at]) / {$intervalMinutes}) * {$intervalMinutes}, 0) as [time],
                        AVG(CAST([amp] as FLOAT)) as [amp],
                        AVG(CAST([voltase] as FLOAT)) as [voltase]
                    FROM [prod_control].[dbo].[{$tableName}]
                    WHERE [id] = ?
                    GROUP BY DATEADD(minute, (DATEDIFF(minute, 0, [created_at]) / {$intervalMinutes}) * {$intervalMinutes}, 0)
                    ORDER BY [time] DESC
                ";
                $query = $db->query($sql, [$circuit]);
            }

            $rows = $query->getResultArray();
            $rows = array_reverse($rows);
            return $this->formatRows($rows);

        } catch (Throwable $e) {
            log_message('error', "Database Error in ChargingModel ({$tableName}): " . $e->getMessage());
            return [
                'error' => "Koneksi Database Gagal ({$tableName}): " . $e->getMessage(),
                'is_db_error' => true,
            ];
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
        $ampOffset = ($line === 'F') ? 1.8 : 0.0;
        $voltOffset = ($line === 'F') ? 3.0 : 0.0;

        for ($i = 0; $i < $points; $i++) {
            $timePoint = $startTime + ($i * $step);
            $baseAmp = 15.0 + $ampOffset + sin($i / 3) * 3 + (mt_rand(-5, 5) / 10);
            $baseVolt = 220.0 + $voltOffset + cos($i / 4) * 5 + (mt_rand(-5, 5) / 10);
            $data[] = [
                'time'    => date('Y-m-d H:i:s', $timePoint),
                'amp'     => round(max(0, $baseAmp), 1),
                'voltase' => round(max(0, $baseVolt), 1),
            ];
        }
        return $data;
    }
}
