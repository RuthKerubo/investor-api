<?php

namespace Tests\Feature;

use App\Models\Investor;
use App\Models\Investment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class InvestorApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_health_endpoint_returns_ok(): void
    {
        $response = $this->getJson('/api/health');

        $response->assertStatus(200)
            ->assertJsonStructure(['status', 'timestamp']);
    }

    public function test_import_endpoint_accepts_csv_file(): void
    {
        $csvContent = "investor_id,name,age,investment_amount,investment_date\n";
        $csvContent .= "1001,John Doe,30,50000.00,15-01-2024\n";
        $csvContent .= "1002,Jane Smith,25,75000.50,20-02-2024\n";

        $file = UploadedFile::fake()->createWithContent('investors.csv', $csvContent);

        $response = $this->postJson('/api/import', ['file' => $file]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Import completed successfully',
            ]);

        $this->assertDatabaseHas('investors', ['investor_id' => 1001, 'name' => 'John Doe']);
        $this->assertDatabaseHas('investors', ['investor_id' => 1002, 'name' => 'Jane Smith']);
    }

    public function test_import_endpoint_validates_file_required(): void
    {
        $response = $this->postJson('/api/import', []);

        $response->assertStatus(422);
    }

    public function test_average_age_endpoint(): void
    {
        Investor::factory()->create(['age' => 30]);
        Investor::factory()->create(['age' => 50]);

        $response = $this->getJson('/api/investors/average-age');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'average_age' => 40.0,
                    'total_investors' => 2,
                ],
            ]);
    }

    public function test_average_investment_endpoint(): void
    {
        $investor = Investor::factory()->create();
        Investment::factory()->create(['investor_id' => $investor->id, 'amount' => 1000]);
        Investment::factory()->create(['investor_id' => $investor->id, 'amount' => 3000]);

        $response = $this->getJson('/api/investors/average-investment');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'average_investment_amount' => 2000.0,
                    'total_investments' => 2,
                ],
            ]);
    }

    public function test_total_investments_endpoint(): void
    {
        $investor = Investor::factory()->create();
        Investment::factory()->count(3)->create([
            'investor_id' => $investor->id,
            'amount' => 1000,
        ]);

        $response = $this->getJson('/api/investors/total-investments');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'total_investments' => 3,
                    'total_amount' => 3000.0,
                ],
            ]);
    }

    public function test_investors_list_endpoint_returns_paginated_data(): void
    {
        Investor::factory()->count(5)->create();

        $response = $this->getJson('/api/investors');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            ]);
    }
}