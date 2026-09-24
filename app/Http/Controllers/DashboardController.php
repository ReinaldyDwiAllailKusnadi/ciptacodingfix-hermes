<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Invoice;
use App\Models\Expense;
use App\Models\Project;
use App\Models\Client;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        // 1. Financial KPIs
        $incomeThisMonth = Invoice::where('status', 'paid')
            ->whereBetween('paid_at', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $expensesThisMonth = Expense::whereBetween('expense_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->sum('amount');

        $netProfitThisMonth = $incomeThisMonth - $expensesThisMonth;

        $unpaidInvoicesAmount = Invoice::where('status', 'unpaid')
            ->sum('amount');

        // 2. Operational KPIs
        $activeProjectsCount = Project::whereIn('status', ['deal_dp', 'in_progress', 'review'])->count();
        $totalClientsCount = Client::count();

        // 3. Cashflow 6-Month Chart Data
        $chartMonths = [];
        $chartIncome = [];
        $chartExpenses = [];
        $chartProfits = [];

        for ($i = 5; $i >= 0; $i--) {
            $monthDate = $now->copy()->subMonths($i);
            $mStart = $monthDate->copy()->startOfMonth();
            $mEnd = $monthDate->copy()->endOfMonth();

            $mLabel = $monthDate->translatedFormat('M Y');
            $mIncome = Invoice::where('status', 'paid')
                ->whereBetween('paid_at', [$mStart, $mEnd])
                ->sum('amount');
            $mExpense = Expense::whereBetween('expense_date', [$mStart->toDateString(), $mEnd->toDateString()])
                ->sum('amount');

            $chartMonths[] = $mLabel;
            $chartIncome[] = (int) $mIncome;
            $chartExpenses[] = (int) $mExpense;
            $chartProfits[] = (int) ($mIncome - $mExpense);
        }

        // 4. Recent Invoices
        $recentInvoices = Invoice::with(['client', 'project'])
            ->latest('id')
            ->limit(5)
            ->get();

        // 5. Active Projects Summary
        $recentProjects = Project::with('client')
            ->whereIn('status', ['deal_dp', 'in_progress', 'review'])
            ->latest('id')
            ->limit(5)
            ->get();

        return Inertia::render('Dashboard/Index', [
            'metrics' => [
                'income_this_month' => (int) $incomeThisMonth,
                'expenses_this_month' => (int) $expensesThisMonth,
                'net_profit_this_month' => (int) $netProfitThisMonth,
                'unpaid_invoices_amount' => (int) $unpaidInvoicesAmount,
                'active_projects_count' => $activeProjectsCount,
                'total_clients_count' => $totalClientsCount,
            ],
            'chart' => [
                'labels' => $chartMonths,
                'income' => $chartIncome,
                'expenses' => $chartExpenses,
                'profits' => $chartProfits,
            ],
            'recent_invoices' => $recentInvoices,
            'recent_projects' => $recentProjects,
        ]);
    }
}
