<?php

require __DIR__ . '/../vendor/codeigniter4/framework/system/Test/bootstrap.php';

$model = new App\Models\ChargingModel();

// Test 1: Model Demo mode Line A
$resA = $model->getChargingData('Circuit 21', 'raw', null, null, true, 'A');
assert(is_array($resA) && count($resA) === 40, 'Result Line A should have 40 data points');
assert(isset($resA[0]['amp']) && isset($resA[0]['voltase']), 'Result Line A should have amp and voltase');
echo "TEST 1 (ChargingModel Demo Mode Line A): PASSED\n";

// Test 2: Model Demo mode Line F
$resF = $model->getChargingData('Circuit 21', 'raw', null, null, true, 'F');
assert(is_array($resF) && count($resF) === 40, 'Result Line F should have 40 data points');
assert(isset($resF[0]['amp']) && isset($resF[0]['voltase']), 'Result Line F should have amp and voltase');
echo "TEST 2 (ChargingModel Demo Mode Line F): PASSED\n";

// Test 3: Catalog Selection Page (index)
$controller = new App\Controllers\Dashboard();
$portalContent = $controller->index();
assert(str_contains($portalContent, 'WETCHARGE'), 'Catalog should contain WETCHARGE');
assert(str_contains($portalContent, 'WET CHARGING'), 'Catalog should contain WET CHARGING');
assert(str_contains($portalContent, 'Charging Line A'), 'Catalog should contain Charging Line A');
assert(str_contains($portalContent, 'Charging Line F'), 'Catalog should contain Charging Line F');
assert(str_contains($portalContent, 'BUKA DASHBOARD WET A'), 'Catalog should contain button for WET A');
assert(str_contains($portalContent, 'BUKA DASHBOARD WET F'), 'Catalog should contain button for WET F');
assert(str_contains($portalContent, 'Kartun Ilustrasi Charging Line A'), 'Catalog should include Line A cartoon illustration');
assert(str_contains($portalContent, 'Kartun Ilustrasi Charging Line F'), 'Catalog should include Line F cartoon illustration');
assert(str_contains($portalContent, 'liveClock'), 'Catalog should have digital live clock in top bar');
assert(!str_contains($portalContent, 'SYSTEM NORMAL'), 'Catalog should not contain SYSTEM NORMAL');
assert(!str_contains($portalContent, 'DATABASE: SQL SERVER'), 'Catalog should not contain DATABASE: SQL SERVER');
assert(!str_contains($portalContent, 'HEAVY DUTY — LOW ANTIMONY SERIES'), 'Catalog should not contain HEAVY DUTY series subtitle');
assert(!str_contains($portalContent, 'MAINTENANCE FREE — HIGH CALCIUM SERIES'), 'Catalog should not contain MAINTENANCE FREE series subtitle');
assert(str_contains($portalContent, 'PT Century Batteries Indonesia. All rights reserved.'), 'Catalog should have clean simple footer');
echo "TEST 3 (Direct 2-Card Catalog View): PASSED\n";

// Test 4: Line Dashboard Views (Line A & Line F)
$lineAContent = $controller->line('A');
assert(str_contains($lineAContent, 'Line A'), 'View should display Line A');
assert(str_contains($lineAContent, 'z_amp_chargingA'), 'View should mention z_amp_chargingA');

$lineFContent = $controller->line('F');
assert(str_contains($lineFContent, 'Line F'), 'View should display Line F');
assert(str_contains($lineFContent, 'z_amp_chargingF'), 'View should mention z_amp_chargingF');
echo "TEST 4 (Dashboard Views for Line A and Line F): PASSED\n";

// Test 5: Dashboard Controller API Endpoint for Line F
$request = service('request');
$request->setGlobal('get', [
    'circuit'  => 'Circuit 21',
    'interval' => 'raw',
    'demo'     => '1',
    'line'     => 'F'
]);
$response = service('response');
$controller->initController($request, $response, service('logger'));
$apiResponse = $controller->api();
$body = $apiResponse->getBody();
$data = json_decode($body, true);
assert(is_array($data) && count($data) === 40, 'API response should contain 40 demo points for Line F');
echo "TEST 5 (API Endpoint for Line F): PASSED\n";

echo "\n>>> ALL 2-CARD CATALOG INTEGRATION TESTS PASSED! <<<\n";
