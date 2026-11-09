<?php

namespace App\Controllers;

use App\Models\ChargingModel;
use CodeIgniter\HTTP\ResponseInterface;

class Dashboard extends BaseController
{
    /**
     * Tampilan utama Dashboard
     */
    public function index(): string
    {
        $activeCircuits = [
            'Circuit 21', 'Circuit 26', 'Circuit 31', 'Circuit 36', 'Circuit 41',
            'Circuit 46', 'Circuit 51', 'Circuit 56', 'Circuit 61', 'Circuit 66'
        ];

        $data = [
            'title'          => 'Dashboard Monitoring - Amp Charging A',
            'activeCircuits' => $activeCircuits,
            'totalCircuits'  => 110,
            'defaultCircuit' => 'Circuit 21',
        ];

        return view('dashboard/index', $data);
    }

    /**
     * JSON API Endpoint untuk Real-time & Historical Data
     */
    public function api(): ResponseInterface
    {
        $circuit     = $this->request->getGet('circuit') ?? 'Circuit 21';
        $intervalStr = $this->request->getGet('interval') ?? 'raw';
        $startStr    = $this->request->getGet('start');
        $endStr      = $this->request->getGet('end');
        $isDemo      = (bool) $this->request->getGet('demo');

        $model  = new ChargingModel();
        $result = $model->getChargingData($circuit, $intervalStr, $startStr, $endStr, $isDemo);

        return $this->response->setJSON($result);
    }
}

