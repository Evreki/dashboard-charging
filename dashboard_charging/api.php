<?php
// api.php
header('Content-Type: application/json');

require_once 'config.php'; 

try {
    $chartData = [];
    
    // Ambil parameter dari frontend

    $circuit = isset($_GET['circuit']) ? $_GET['circuit'] : 'Circuit 21';
    $intervalStr = isset($_GET['interval']) ? $_GET['interval'] : 'raw';
    $startStr = isset($_GET['start']) ? $_GET['start'] : '';
    $endStr = isset($_GET['end']) ? $_GET['end'] : '';

    if (!empty($startStr)) {
        $startStr = date('Y-m-d H:i:s', strtotime($startStr));
    }
    if (!empty($endStr)) {
        $endStr = date('Y-m-d H:i:s', strtotime($endStr));
    }

    $isHistorical = !empty($startStr) && !empty($endStr);
    
    // Konversi string interval ke menit
    $intervalMinutes = 1;
    $isRaw = false;
    if ($intervalStr === 'raw') $isRaw = true;
    elseif ($intervalStr === '10m') $intervalMinutes = 10;
    elseif ($intervalStr === '30m') $intervalMinutes = 30;
    elseif ($intervalStr === '1h') $intervalMinutes = 60;
    
    if ($isHistorical) {
        // --- MODE HISTORICAL (TANGGAL DITENTUKAN) ---
        if ($isRaw) {
            $query = "
                SELECT 
                    [created_at] as [time],
                    CAST([amp] as FLOAT) as [amp],
                    CAST([voltase] as FLOAT) as [voltase]
                FROM [prod_control].[dbo].[z_amp_chargingA]
                WHERE [id] = ?
                  AND [created_at] >= ? 
                  AND [created_at] <= ?
                ORDER BY [time] ASC
            ";
        } else {
            $query = "
                SELECT 
                    DATEADD(minute, (DATEDIFF(minute, 0, [created_at]) / $intervalMinutes) * $intervalMinutes, 0) as [time],
                    AVG(CAST([amp] as FLOAT)) as [amp],
                    AVG(CAST([voltase] as FLOAT)) as [voltase]
                FROM [prod_control].[dbo].[z_amp_chargingA]
                WHERE [id] = ?
                  AND [created_at] >= ? 
                  AND [created_at] <= ?
                GROUP BY DATEADD(minute, (DATEDIFF(minute, 0, [created_at]) / $intervalMinutes) * $intervalMinutes, 0)
                ORDER BY [time] ASC
            ";
        }
        
        $params = array(
            $circuit, 
            $startStr, $endStr
        );
        
        $stmt = sqlsrv_query($conn, $query, $params);
        
        if ($stmt === false) {
            die(json_encode(["error" => "Query Error: ", "details" => sqlsrv_errors()]));
        }

        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $timeStr = is_object($row['time']) ? $row['time']->format('Y-m-d H:i:s') : date('Y-m-d H:i:s', strtotime($row['time']));
            $chartData[] = [
                'time' => $timeStr,
                'amp' => floor(((float)$row['amp']) / 10) / 10,
                'voltase' => floor(((float)$row['voltase']) / 10) / 10
            ];
        }

    } else {
        // --- MODE LIVE (TANPA TANGGAL, TAPI MENGGUNAKAN INTERVAL) ---
        if ($isRaw) {
            $query = "
                SELECT TOP 60
                    [created_at] as [time],
                    CAST([amp] as FLOAT) as [amp],
                    CAST([voltase] as FLOAT) as [voltase]
                FROM [prod_control].[dbo].[z_amp_chargingA]
                WHERE [id] = ?
                ORDER BY [time] DESC
            ";
        } else {
            $query = "
                SELECT TOP 60
                    DATEADD(minute, (DATEDIFF(minute, 0, [created_at]) / $intervalMinutes) * $intervalMinutes, 0) as [time],
                    AVG(CAST([amp] as FLOAT)) as [amp],
                    AVG(CAST([voltase] as FLOAT)) as [voltase]
                FROM [prod_control].[dbo].[z_amp_chargingA]
                WHERE [id] = ?
                GROUP BY DATEADD(minute, (DATEDIFF(minute, 0, [created_at]) / $intervalMinutes) * $intervalMinutes, 0)
                ORDER BY [time] DESC
            ";
        }
        
        $params = array(
            $circuit
        );
        
        $stmt = sqlsrv_query($conn, $query, $params);

        
        if ($stmt === false) {
            die(json_encode(["error" => "Query Error: ", "details" => sqlsrv_errors()]));
        }
        
        $data = [];
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $data[] = $row;
        }
        
        // Dibalik agar grafiknya berjalan maju (dari kiri ke kanan)
        $data = array_reverse($data);
        
        foreach ($data as $row) {
            $timeStr = is_object($row['time']) ? $row['time']->format('Y-m-d H:i:s') : date('Y-m-d H:i:s', strtotime($row['time']));
            $chartData[] = [
                'time' => $timeStr,
                'amp' => floor(((float)$row['amp']) / 10) / 10,
                'voltase' => floor(((float)$row['voltase']) / 10) / 10
            ];
        }
    }
    
    echo json_encode($chartData);
    
} catch (Exception $e) {
    echo json_encode(["error" => "Error sistem: " . $e->getMessage()]);
}
?>
