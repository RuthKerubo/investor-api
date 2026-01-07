<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CsvImportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    public function __construct(
        private readonly CsvImportService $csvImportService
    ) {}

    /**
     * Import investors from CSV file.
     * 
     * POST /api/import
     */
    public function import(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        try {
            $stats = $this->csvImportService->importFromCsv($request->file('file'));

            $hasErrors = !empty($stats['errors']);

            return response()->json([
                'success' => true,
                'message' => $hasErrors 
                    ? 'Import completed with some errors' 
                    : 'Import completed successfully',
                'data' => $stats,
            ], $hasErrors ? 207 : 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Import failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}