<?php

namespace App\Controllers;

use App\Models\ChargingModel;
use CodeIgniter\HTTP\ResponseInterface;

class Dashboard extends BaseController
{
    /**
     * Halaman Portal Pemilihan (Hub Landing Page)
     */
    public function index(): string
    {
        $lines = [
            'A' => [
                'code'        => 'A',
                'name'        => 'Charging Line A',
                'subtitle'    => 'Production Charging Unit A Monitoring',
                'table'       => 'z_amp_chargingA',
                'badge_color' => '#0ea5e9', // Blue/Cyan
                'bg_gradient' => 'linear-gradient(135deg, #0284c7 0%, #0ea5e9 100%)',
                'icon'        => 'battery-charging',
                'circuits'    => 110,
                'status'      => 'Online / Active',
                'description' => 'Monitoring realtime arus (Ampere) dan tegangan (Voltase) untuk seluruh sirkuit Line A.'
            ],
            'F' => [
                'code'        => 'F',
                'name'        => 'Charging Line F',
                'subtitle'    => 'Production Charging Unit F Monitoring',
                'table'       => 'z_amp_chargingF',
                'badge_color' => '#8b5cf6', // Purple/Violet
                'bg_gradient' => 'linear-gradient(135deg, #6d28d9 0%, #8b5cf6 100%)',
                'icon'        => 'zap',
                'circuits'    => 110,
                'status'      => 'Online / Active',
                'description' => 'Monitoring realtime arus (Ampere) dan tegangan (Voltase) untuk seluruh sirkuit Line F.'
            ]
        ];

        $data = [
            'title' => 'Portal Monitoring Charging - Line Selection',
            'lines' => $lines
        ];

        return view('dashboard/portal', $data);
    }

    /**
     * Tampilan Dashboard spesifik untuk Line A atau Line F
     */
    public function line(string $line = 'A'): string
    {
        $lineClean = (strtoupper(trim($line)) === 'F') ? 'F' : 'A';

        $activeCircuits = [
            'Circuit 21', 'Circuit 26', 'Circuit 31', 'Circuit 36', 'Circuit 41',
            'Circuit 46', 'Circuit 51', 'Circuit 56', 'Circuit 61', 'Circuit 66'
        ];

        $data = [
            'title'          => 'Dashboard Monitoring - Amp Charging ' . $lineClean,
            'line'           => $lineClean,
            'activeCircuits' => $activeCircuits,
            'totalCircuits'  => 110,
            'defaultCircuit' => 'Circuit 21',
        ];

        return view('dashboard/index', $data);
    }

    /**
     * JSON API Endpoint untuk Real-time & Historical Data (Line A atau Line F)
     */
    public function api(): ResponseInterface
    {
        $circuit     = $this->request->getGet('circuit') ?? 'Circuit 21';
        $intervalStr = $this->request->getGet('interval') ?? 'raw';
        $startStr    = $this->request->getGet('start');
        $endStr      = $this->request->getGet('end');
        $isDemo      = (bool) $this->request->getGet('demo');
        $line        = $this->request->getGet('line') ?? 'A';

        $model  = new ChargingModel();
        $result = $model->getChargingData($circuit, $intervalStr, $startStr, $endStr, $isDemo, $line);

        return $this->response
            ->setHeader('X-Data-Source', $model->lastSource)
            ->setHeader('X-DB-Connected', $model->isDbConnected ? '1' : '0')
            ->setHeader('X-DB-Message', rawurlencode($model->lastMessage ?? ''))
            ->setJSON($result);
    }
}
