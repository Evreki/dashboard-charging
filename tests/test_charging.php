<?php

require __DIR__ . '/../vendor/codeigniter4/framework/system/Test/bootstrap.php';

// Test 1: Model Demo mode
$model = new App\Models\ChargingModel();
$res = $model->getChargingData('Circuit 21', 'raw', null, null, true);
assert(is_array($res), 'Result should be array');
assert(count($res) === 40, 'Result count should be 40');
assert(isset($res[0]['amp']), 'Result should have amp');
assert(isset($res[0]['voltase']), 'Result should have voltase');
assert(isset($res[0]['time']), 'Result should have time');
echo "TEST 1 (ChargingModel Demo Mode): PASSED (" . count($res) . " data points)\n";

// Test 2: Database offline handling (should return error array gracefully without crashing)
$resDb = $model->getChargingData('Circuit 21', 'raw');
assert(is_array($resDb), 'Database result should be array');
echo "TEST 2 (Database Graceful Error Handling): PASSED\n";
if (isset($resDb['error'])) {
    echo "  -> Handled offline database message: " . substr($resDb['error'], 0, 60) . "...\n";
} else {
    echo "  -> Connected to real database! (" . count($resDb) . " records)\n";
}

// Test 3: Dashboard Controller Index View
$controller = new App\Controllers\Dashboard();
$viewContent = $controller->index();
assert(str_contains($viewContent, 'Production Dashboard'), 'View should contain Production Dashboard');
assert(str_contains($viewContent, 'Ampere Charging A'), 'View should contain Ampere Charging A');
assert(str_contains($viewContent, 'Circuit 21'), 'View should contain Circuit 21');
assert(str_contains($viewContent, 'api/charging'), 'View should contain API endpoint');
echo "TEST 3 (Dashboard Controller Index View): PASSED\n";

// Test 4: Dashboard Controller API Endpoint
$request = service('request');
$request->setGlobal('get', [
    'circuit'  => 'Circuit 21',
    'interval' => 'raw',
    'demo'     => '1'
]);
$response = service('response');
$controller->initController($request, $response, service('logger'));
$apiResponse = $controller->api();
$body = $apiResponse->getBody();
$data = json_decode($body, true);
assert(is_array($data), 'API response should be JSON array');
assert(count($data) === 40, 'API response should contain 40 demo points');
assert(isset($data[0]['amp']) && isset($data[0]['voltase']), 'API points should have amp and voltase');
echo "TEST 4 (Dashboard Controller API Endpoint): PASSED\n";

echo "\n>>> ALL CI4 INTEGRATION TESTS PASSED SUCCESSFULLY! <<<\n";
