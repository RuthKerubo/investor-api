<?php

namespace App\Services;

use App\Models\Investor;
use App\Models\Investment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class InvestorService
{
    /**
     * Get average age of all investors.
     */
    public function getAverageAge(): array
    {
        $result = Investor::selectRaw('AVG(age) as average_age, COUNT(*) as total_investors')
            ->first();

        return [
            'average_age' => round($result->average_age ?? 0, 2),
            'total_investors' => $result->total_investors ?? 0,
        ];
    }

    /**
     * Get average investment amount.
     */
    public function getAverageInvestmentAmount(): array
    {
        $result = Investment::selectRaw('AVG(amount) as average_amount, COUNT(*) as total_investments')
            ->first();

        return [
            'average_investment_amount' => round($result->average_amount ?? 0, 2),
            'total_investments' => $result->total_investments ?? 0,
        ];
    }

    /**
     * Get total number of investments.
     */
    public function getTotalInvestments(): array
    {
        return [
            'total_investments' => Investment::count(),
            'total_amount' => round(Investment::sum('amount'), 2),
        ];
    }

    /**
     * Get all investors with their total investment amounts (paginated).
     */
    public function getAllInvestorsWithInvestments(int $perPage = 100): LengthAwarePaginator
    {
        return Investor::select([
                'investors.id',
                'investors.investor_id',
                'investors.name',
                'investors.age',
            ])
            ->selectRaw('COALESCE(SUM(investments.amount), 0) as total_investment_amount')
            ->leftJoin('investments', 'investors.id', '=', 'investments.investor_id')
            ->groupBy('investors.id', 'investors.investor_id', 'investors.name', 'investors.age')
            ->orderBy('investors.investor_id')
            ->paginate($perPage);
    }
}