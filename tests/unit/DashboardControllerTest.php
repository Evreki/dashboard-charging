<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use App\Models\ChargingModel;

/**
 * @internal
 */
final class DashboardControllerTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    public function testDashboardPageRenders(): void
    {
        $result = $this->get('/');
        $result->assertStatus(200);
        $result->assertSee('Production Dashboard');
        $result->assertSee('Ampere Charging A');
    }

    public function testChargingApiReturnsValidData(): void
    {
        $result = $this->get('api/charging?circuit=Circuit%2021&interval=raw&demo=1');
        $result->assertStatus(200);
        
        $json = json_decode($result->getJSON(), true);
        $this->assertIsArray($json);
        $this->assertNotEmpty($json);
        $this->assertArrayHasKey('time', $json[0]);
        $this->assertArrayHasKey('amp', $json[0]);
        $this->assertArrayHasKey('voltase', $json[0]);
    }

    public function testModelDemoData(): void
    {
        $model = new ChargingModel();
        $data = $model->getChargingData('Circuit 21', 'raw', null, null, true);
        
        $this->assertIsArray($data);
        $this->assertCount(40, $data);
        $this->assertArrayHasKey('time', $data[0]);
        $this->assertArrayHasKey('amp', $data[0]);
        $this->assertArrayHasKey('voltase', $data[0]);
    }
}

