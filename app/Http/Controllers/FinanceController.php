<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Invoice;
use App\Models\Expense;
use App\Models\Project;
use App\Models\Client;
use Carbon\Carbon;

class FinanceController extends Controller
{
    // ==========================================
    // 1. INVOICES
    // ==========================================
    public function invoicesIndex(Request $request)
    {
        $status = $request->input('status', 'all');
        $query = Invoice::with(['client', 'project'])->latest('id');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $invoices = $query->paginate(15)->withQueryString();
        $clients = Client::orderBy('name')->get(['id', 'name', 'company', 'whatsapp']);
        $projects = Project::orderBy('title')->get(['id', 'client_id', 'code', 'title', 'total_budget']);

        $summary = [
            'total_paid' => Invoice::where('status', 'paid')->sum('amount'),
            'total_unpaid' => Invoice::where('status', 'unpaid')->sum('amount'),
            'total_overdue' => Invoice::where('status', 'overdue')->sum('amount'),
            'count_all' => Invoice::count(),
            'count_paid' => Invoice::where('status', 'paid')->count(),
            'count_unpaid' => Invoice::where('status', 'unpaid')->count(),
        ];

        return Inertia::render('Finance/Invoices/Index', [
            'invoices' => $invoices,
            'clients' => $clients,
            'projects' => $projects,
            'summary' => $summary,
            'current_status' => $status,
        ]);
    }

    public function invoiceStore(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
            'type' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'issue_date' => 'required|date',
            'due_date' => 'required|date',
            'payment_method' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        // Auto-generate invoice number: INV/YYYY/MM/XXXX
        $year = Carbon::now()->format('Y');
        $month = Carbon::now()->format('m');
        $count = Invoice::whereYear('created_at', $year)->count() + 1;
        $invNumber = sprintf("INV/%s/%s/%03d", $year, $month, $count);

        Invoice::create([
            ...$validated,
            'invoice_number' => $invNumber,
            'status' => 'unpaid',
        ]);

        return redirect()->back()->with('success', "Invoice $invNumber berhasil dibuat!");
    }

    public function invoiceUpdateStatus(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'status' => 'required|in:unpaid,paid,overdue,cancelled',
            'payment_method' => 'nullable|string',
        ]);

        $invoice->update([
            'status' => $validated['status'],
            'payment_method' => $validated['payment_method'] ?? $invoice->payment_method,
            'paid_at' => $validated['status'] === 'paid' ? Carbon::now() : null,
        ]);

        return redirect()->back()->with('success', "Status invoice #{$invoice->invoice_number} diperbarui!");
    }

    public function invoiceDestroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->back()->with('success', "Invoice berhasil dihapus.");
    }

    // ==========================================
    // 2. EXPENSES
    // ==========================================
    public function expensesIndex(Request $request)
    {
        $category = $request->input('category', 'all');
        $query = Expense::with('project')->latest('expense_date');

        if ($category !== 'all') {
            $query->where('category', $category);
        }

        $expenses = $query->paginate(20)->withQueryString();
        $projects = Project::orderBy('title')->get(['id', 'code', 'title']);

        $categorySummary = [
            'server_cloud' => Expense::where('category', 'server_cloud')->sum('amount'),
            'freelance_salary' => Expense::where('category', 'freelance_salary')->sum('amount'),
            'tools_licenses' => Expense::where('category', 'tools_licenses')->sum('amount'),
            'operational' => Expense::where('category', 'operational')->sum('amount'),
            'marketing' => Expense::where('category', 'marketing')->sum('amount'),
            'total' => Expense::sum('amount'),
        ];

        return Inertia::render('Finance/Expenses/Index', [
            'expenses' => $expenses,
            'projects' => $projects,
            'summary' => $categorySummary,
            'current_category' => $category,
        ]);
    }

    public function expenseStore(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|in:server_cloud,freelance_salary,tools_licenses,operational,marketing',
            'amount' => 'required|numeric|min:1',
            'expense_date' => 'required|date',
            'project_id' => 'nullable|exists:projects,id',
            'notes' => 'nullable|string',
        ]);

        Expense::create($validated);
        return redirect()->back()->with('success', 'Pengeluaran berhasil dicatat!');
    }

    public function expenseDestroy(Expense $expense)
    {
        $expense->delete();
        return redirect()->back()->with('success', 'Catatan pengeluaran berhasil dihapus.');
    }

    // ==========================================
    // 3. CASH FLOW & PROFIT / LOSS STATEMENT
    // ==========================================
    public function cashflowIndex()
    {
        $now = Carbon::now();
        $yearlyMonths = [];

        for ($m = 1; $m <= 12; $m++) {
            $date = Carbon::create($now->year, $m, 1);
            $start = $date->copy()->startOfMonth();
            $end = $date->copy()->endOfMonth();

            $income = Invoice::where('status', 'paid')
                ->whereBetween('paid_at', [$start, $end])
                ->sum('amount');

            $expense = Expense::whereBetween('expense_date', [$start->toDateString(), $end->toDateString()])
                ->sum('amount');

            $yearlyMonths[] = [
                'month_number' => $m,
                'month_name' => $date->translatedFormat('F'),
                'income' => (int) $income,
                'expense' => (int) $expense,
                'profit' => (int) ($income - $expense),
            ];
        }

        $totalYearIncome = array_sum(array_column($yearlyMonths, 'income'));
        $totalYearExpense = array_sum(array_column($yearlyMonths, 'expense'));
        $totalYearProfit = $totalYearIncome - $totalYearExpense;

        return Inertia::render('Finance/Cashflow/Index', [
            'year' => $now->year,
            'months' => $yearlyMonths,
            'total_income' => $totalYearIncome,
            'total_expense' => $totalYearExpense,
            'total_profit' => $totalYearProfit,
        ]);
    }
}
