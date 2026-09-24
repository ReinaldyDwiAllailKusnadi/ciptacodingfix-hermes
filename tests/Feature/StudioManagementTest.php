<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Client;
use App\Models\Project;
use App\Models\Invoice;
use App\Models\Expense;
use App\Models\Portfolio;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class StudioManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin Studio',
            'email' => 'admin@ciptacoding.com',
            'password' => Hash::make('ciptacoding2026'),
            'role' => 'admin',
        ]);
    }

    /**
     * 1. Public Clean Pages & SEO Redirects
     */
    public function test_public_clean_routes_accessible(): void
    {
        $this->get('/')->assertStatus(200);
        $this->get('/portofolio')->assertStatus(200);
        $this->get('/layanan')->assertStatus(200);
        $this->get('/cara-order')->assertStatus(200);
        $this->get('/tanya-jawab')->assertStatus(200);
    }

    public function test_legacy_html_urls_redirect_permanently(): void
    {
        $this->get('/index.html')->assertRedirect('/');
        $this->get('/portofolio.html')->assertRedirect('/portofolio');
        $this->get('/layanan.html')->assertRedirect('/layanan');
        $this->get('/cara-order.html')->assertRedirect('/cara-order');
        $this->get('/tanya-jawab.html')->assertRedirect('/tanya-jawab');
    }

    public function test_login_page_renders_for_guest(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_guest_is_redirected_to_login_when_accessing_dashboard(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    /**
     * 2. Authentication Flow
     */
    public function test_login_with_valid_credentials_succeeds(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@ciptacoding.com',
            'password' => 'ciptacoding2026',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($this->admin);
    }

    public function test_login_with_invalid_credentials_fails(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@ciptacoding.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_authenticated_user_can_logout(): void
    {
        $response = $this->actingAs($this->admin)->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    /**
     * 3. Dashboard Access
     */
    public function test_dashboard_renders_metrics_for_admin(): void
    {
        $response = $this->actingAs($this->admin)->get('/dashboard');

        $response->assertStatus(200);
    }

    /**
     * 4. Client CRM Operations
     */
    public function test_client_crud_lifecycle(): void
    {
        // 4a. Index
        $response = $this->actingAs($this->admin)->get('/clients');
        $response->assertStatus(200);

        // 4b. Store
        $createResponse = $this->actingAs($this->admin)->post('/clients', [
            'name' => 'Budi Pratama',
            'company' => 'PT Jaya Bersama',
            'whatsapp' => '081234567890',
            'email' => 'budi@jayabersama.co.id',
            'status' => 'active',
            'notes' => 'Klien loyal',
        ]);
        $createResponse->assertSessionHas('success');

        $client = Client::where('email', 'budi@jayabersama.co.id')->first();
        $this->assertNotNull($client);
        $this->assertEquals('PT Jaya Bersama', $client->company);

        // 4c. Update
        $updateResponse = $this->actingAs($this->admin)->put("/clients/{$client->id}", [
            'name' => 'Budi Pratama Updated',
            'company' => 'PT Jaya Bersama Tbk',
            'whatsapp' => '081234567890',
            'email' => 'budi@jayabersama.co.id',
            'status' => 'active',
        ]);
        $updateResponse->assertSessionHas('success');
        $this->assertEquals('PT Jaya Bersama Tbk', $client->fresh()->company);

        // 4d. Destroy
        $deleteResponse = $this->actingAs($this->admin)->delete("/clients/{$client->id}");
        $deleteResponse->assertSessionHas('success');
        $this->assertNull(Client::find($client->id));
    }

    /**
     * 5. Project Management Operations
     */
    public function test_project_crud_lifecycle(): void
    {
        $client = Client::create([
            'name' => 'PT Retail Nusantara',
            'status' => 'active',
        ]);

        // 5a. Store
        $createResponse = $this->actingAs($this->admin)->post('/projects', [
            'client_id' => $client->id,
            'code' => 'PRJ-RETAIL-01',
            'title' => 'Sistem Kasir Cloud Multi-Cabang',
            'category' => 'Web App',
            'status' => 'in_progress',
            'total_budget' => 25000000,
            'progress_percent' => 50,
        ]);
        $createResponse->assertSessionHas('success');

        $project = Project::where('code', 'PRJ-RETAIL-01')->first();
        $this->assertNotNull($project);
        $this->assertEquals(25000000, $project->total_budget);

        // 5b. Update
        $updateResponse = $this->actingAs($this->admin)->put("/projects/{$project->id}", [
            'client_id' => $client->id,
            'code' => 'PRJ-RETAIL-01',
            'title' => 'Sistem Kasir Cloud Multi-Cabang v2',
            'category' => 'Web App',
            'status' => 'review',
            'total_budget' => 30000000,
            'progress_percent' => 90,
        ]);
        $updateResponse->assertSessionHas('success');
        $this->assertEquals(30000000, $project->fresh()->total_budget);
        $this->assertEquals('review', $project->fresh()->status);

        // 5c. Destroy
        $deleteResponse = $this->actingAs($this->admin)->delete("/projects/{$project->id}");
        $deleteResponse->assertSessionHas('success');
        $this->assertNull(Project::find($project->id));
    }

    /**
     * 6. Financial Management: Invoices & Expenses
     */
    public function test_invoice_and_expense_lifecycle(): void
    {
        $client = Client::create([
            'name' => 'PT Solusi Finansial',
            'status' => 'active',
        ]);

        // 6a. Invoice Index
        $response = $this->actingAs($this->admin)->get('/finance/invoices');
        $response->assertStatus(200);

        // 6b. Create Invoice
        $invCreateResponse = $this->actingAs($this->admin)->post('/finance/invoices', [
            'client_id' => $client->id,
            'type' => 'DP 50%',
            'amount' => 10000000,
            'issue_date' => Carbon::now()->toDateString(),
            'due_date' => Carbon::now()->addDays(7)->toDateString(),
            'notes' => 'Pembayaran Termin 1',
        ]);
        $invCreateResponse->assertSessionHas('success');

        $invoice = Invoice::where('client_id', $client->id)->first();
        $this->assertNotNull($invoice);
        $this->assertEquals('unpaid', $invoice->status);
        $this->assertStringStartsWith('INV/', $invoice->invoice_number);

        // 6c. Update Invoice Status to Paid
        $statusResponse = $this->actingAs($this->admin)->put("/finance/invoices/{$invoice->id}/status", [
            'status' => 'paid',
            'payment_method' => 'BCA Transfer',
        ]);
        $statusResponse->assertSessionHas('success');
        $this->assertEquals('paid', $invoice->fresh()->status);
        $this->assertNotNull($invoice->fresh()->paid_at);

        // 6d. Expenses Index
        $expIndex = $this->actingAs($this->admin)->get('/finance/expenses');
        $expIndex->assertStatus(200);

        // 6e. Create Expense
        $expCreateResponse = $this->actingAs($this->admin)->post('/finance/expenses', [
            'title' => 'Server VPS Cloud Bulanan',
            'category' => 'server_cloud',
            'amount' => 450000,
            'expense_date' => Carbon::now()->toDateString(),
            'notes' => 'Langganan DigitalOcean',
        ]);
        $expCreateResponse->assertSessionHas('success');

        $expense = Expense::where('title', 'Server VPS Cloud Bulanan')->first();
        $this->assertNotNull($expense);
        $this->assertEquals(450000, $expense->amount);

        // 6f. Cashflow Overview
        $cashflowResponse = $this->actingAs($this->admin)->get('/finance/cashflow');
        $cashflowResponse->assertStatus(200);
    }

    /**
     * 7. Portfolio Showcase CMS & JSON Sync
     */
    public function test_portfolio_cms_and_json_synchronization(): void
    {
        $jsonPath = public_path('data/portofolio.json');
        $backupContent = file_exists($jsonPath) ? file_get_contents($jsonPath) : null;

        try {
            $response = $this->actingAs($this->admin)->get('/portfolio-manager');
            $response->assertStatus(200);

            // Create portfolio
            $createResponse = $this->actingAs($this->admin)->post('/portfolio-manager', [
                'title' => 'Sistem ERP Logistik Laut',
                'code' => 'TEST-PORT-01',
                'category_label' => 'Web App & IoT',
                'categories' => ['custom_web', 'fullstack'],
                'description' => 'Aplikasi tracking kapal real-time',
                'image' => 'assets/images/pos-erp-multi-outlet.svg',
                'badge_top' => ['Vue 3', 'Laravel 11'],
                'badge_bottom' => 'Siap Pakai',
                'tech_stack' => ['Laravel', 'Vue.js', 'MySQL'],
                'highlight' => 'Hemat biaya 40%',
                'cta_text' => 'Konsultasi Sekarang',
                'whatsapp_text' => 'Halo CiptaCoding mau tanya portofolio',
            ]);
            $createResponse->assertSessionHas('success');

            $portfolio = Portfolio::where('code', 'TEST-PORT-01')->first();
            $this->assertNotNull($portfolio);
            $this->assertEquals('Sistem ERP Logistik Laut', $portfolio->title);

            // Check if public/data/portofolio.json was updated
            $this->assertFileExists($jsonPath);
            $jsonContent = file_get_contents($jsonPath);
            $this->assertStringContainsString('TEST-PORT-01', $jsonContent);

            // Clean up
            $deleteResponse = $this->actingAs($this->admin)->delete("/portfolio-manager/{$portfolio->id}");
            $deleteResponse->assertSessionHas('success');
            $this->assertNull(Portfolio::find($portfolio->id));
        } finally {
            if ($backupContent !== null) {
                file_put_contents($jsonPath, $backupContent);
            }
        }
    }
}
