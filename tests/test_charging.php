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

// Test 3: Portal Selection Landing Page (index)
$controller = new App\Controllers\Dashboard();
$portalContent = $controller->index();
assert(str_contains($portalContent, 'Pilih Lini Charging'), 'Portal should contain Pilih Lini Charging');
assert(str_contains($portalContent, 'Charging Line A'), 'Portal should contain Charging Line A');
assert(str_contains($portalContent, 'Charging Line F'), 'Portal should contain Charging Line F');
assert(str_contains($portalContent, 'z_amp_chargingA'), 'Portal should mention z_amp_chargingA');
assert(str_contains($portalContent, 'z_amp_chargingF'), 'Portal should mention z_amp_chargingF');
echo "TEST 3 (Portal Selection View): PASSED\n";

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

echo "\n>>> ALL LINE A & LINE F CI4 INTEGRATION TESTS PASSED! <<<\n";
