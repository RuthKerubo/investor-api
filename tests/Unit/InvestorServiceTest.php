<?php

namespace Tests\Unit;

use App\Models\Investor;
use App\Models\Investment;
use App\Services\InvestorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvestorServiceTest extends TestCase
{
    use RefreshDatabase;

    private InvestorService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new InvestorService();
    }

    public function test_it_calculates_average_age_correctly(): void
    {
        Investor::factory()->create(['age' => 30]);
        Investor::factory()->create(['age' => 40]);
        Investor::factory()->create(['age' => 50]);

        $result = $this->service->getAverageAge();

        $this->assertEquals(40.0, $result['average_age']);
        $this->assertEquals(3, $result['total_investors']);
    }

    public function test_it_returns_zero_when_no_investors(): void
    {
        $result = $this->service->getAverageAge();

        $this->assertEquals(0, $result['average_age']);
        $this->assertEquals(0, $result['total_investors']);
    }

    public function test_it_calculates_average_investment_correctly(): void
    {
        $investor = Investor::factory()->create();

        Investment::factory()->create(['investor_id' => $investor->id, 'amount' => 1000]);
        Investment::factory()->create(['investor_id' => $investor->id, 'amount' => 2000]);
        Investment::factory()->create(['investor_id' => $investor->id, 'amount' => 3000]);

        $result = $this->service->getAverageInvestmentAmount();

        $this->assertEquals(2000.0, $result['average_investment_amount']);
        $this->assertEquals(3, $result['total_investments']);
    }

    public function test_it_calculates_total_investments_correctly(): void
    {
        $investor = Investor::factory()->create();

        Investment::factory()->count(5)->create([
            'investor_id' => $investor->id,
            'amount' => 1000,
        ]);

        $result = $this->service->getTotalInvestments();

        $this->assertEquals(5, $result['total_investments']);
        $this->assertEquals(5000.0, $result['total_amount']);
    }
}