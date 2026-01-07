<?php

namespace App\Services;

use App\Models\Investor;
use App\Models\Investment;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class CsvImportService
{
    /**
     * Import investors and investments from a CSV file.
     */
    public function importFromCsv(UploadedFile $file): array
    {
        $stats = [
            'investors_created' => 0,
            'investors_updated' => 0,
            'investments_created' => 0,
            'errors' => [],
        ];

        $handle = fopen($file->getPathname(), 'r');
        
        // Skip header row
        $header = fgetcsv($handle);
        
        $rowNumber = 1;
        
        DB::beginTransaction();
        
        try {
            while (($row = fgetcsv($handle)) !== false) {
                $rowNumber++;
                
                try {
                    $data = array_combine($header, $row);
                    $this->processRow($data, $stats);
                } catch (\Exception $e) {
                    $stats['errors'][] = "Row {$rowNumber}: " . $e->getMessage();
                }
            }
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        } finally {
            fclose($handle);
        }
        
        return $stats;
    }

    /**
     * Process a single CSV row.
     */
    private function processRow(array $data, array &$stats): void
    {
        // Validate required fields
        $this->validateRow($data);
        
        // Create or update investor
        $investor = Investor::updateOrCreate(
            ['investor_id' => $data['investor_id']],
            [
                'name' => $data['name'],
                'age' => $data['age'],
            ]
        );
        
        if ($investor->wasRecentlyCreated) {
            $stats['investors_created']++;
        } else {
            $stats['investors_updated']++;
        }
        
        // Parse date (format: DD-MM-YYYY)
        $investmentDate = Carbon::createFromFormat('d-m-Y', $data['investment_date'])->format('Y-m-d');
        
        // Create investment
        Investment::updateOrCreate(
            [
                'investor_id' => $investor->id,
                'investment_date' => $investmentDate,
            ],
            [
                'amount' => $data['investment_amount'],
            ]
        );
        
        $stats['investments_created']++;
    }

    /**
     * Validate a CSV row.
     */
    private function validateRow(array $data): void
    {
        $required = ['investor_id', 'name', 'age', 'investment_amount', 'investment_date'];
        
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new \InvalidArgumentException("Missing required field: {$field}");
            }
        }
        
        if (!is_numeric($data['investor_id']) || $data['investor_id'] <= 0) {
            throw new \InvalidArgumentException("Invalid investor_id");
        }
        
        if (!is_numeric($data['age']) || $data['age'] < 0 || $data['age'] > 150) {
            throw new \InvalidArgumentException("Invalid age");
        }
        
        if (!is_numeric($data['investment_amount']) || $data['investment_amount'] < 0) {
            throw new \InvalidArgumentException("Invalid investment_amount");
        }
    }
}