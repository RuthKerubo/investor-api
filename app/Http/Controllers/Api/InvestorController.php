<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\InvestorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvestorController extends Controller
{
    public function __construct(
        private readonly InvestorService $investorService
    ) {}

    /**
     * Get average age of all investors.
     * 
     * GET /api/investors/average-age
     */
    public function averageAge(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->investorService->getAverageAge(),
        ]);
    }

    /**
     * Get average investment amount.
     * 
     * GET /api/investors/average-investment
     */
    public function averageInvestment(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->investorService->getAverageInvestmentAmount(),
        ]);
    }

    /**
     * Get total number of investments.
     * 
     * GET /api/investors/total-investments
     */
    public function totalInvestments(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->investorService->getTotalInvestments(),
        ]);
    }

    /**
     * Get all investors with their investment amounts.
     * 
     * GET /api/investors
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->query('per_page', 100), 1000);
        
        $investors = $this->investorService->getAllInvestorsWithInvestments($perPage);

        return response()->json([
            'success' => true,
            'data' => $investors->items(),
            'meta' => [
                'current_page' => $investors->currentPage(),
                'last_page' => $investors->lastPage(),
                'per_page' => $investors->perPage(),
                'total' => $investors->total(),
            ],
        ]);
    }
}