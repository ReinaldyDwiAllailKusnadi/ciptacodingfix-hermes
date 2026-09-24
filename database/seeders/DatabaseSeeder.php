<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Client;
use App\Models\Project;
use App\Models\Invoice;
use App\Models\Expense;
use App\Models\Portfolio;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin User
        User::updateOrCreate(
            ['email' => 'admin@ciptacoding.com'],
            [
                'name' => 'Admin CiptaCoding',
                'password' => Hash::make('ciptacoding2026'),
                'role' => 'owner',
            ]
        );

        // 2. Sample Clients
        $client1 = Client::updateOrCreate(['name' => 'Budi Pratama'], [
            'company' => 'Kopi Serasi & Bakery',
            'whatsapp' => '081234567890',
            'email' => 'budi@kopiserasi.id',
            'address' => 'Bandung, Jawa Barat',
            'status' => 'active',
            'notes' => 'Klien loyal aplikasi POS Kasir 3 outlet.'
        ]);

        $client2 = Client::updateOrCreate(['name' => 'Dinda Wulandari'], [
            'company' => 'CV Rempah Nusantara Mandiri',
            'whatsapp' => '081987654321',
            'email' => 'dinda@rempahnusantara.com',
            'address' => 'Surabaya, Jawa Timur',
            'status' => 'active',
            'notes' => 'Katalog ekspor produk UMKM ke pasar global.'
        ]);

        $client3 = Client::updateOrCreate(['name' => 'Rizky Ananda'], [
            'company' => 'PT Prima Logistik Sentosa',
            'whatsapp' => '087711223344',
            'email' => 'rizky@primalogistik.co.id',
            'address' => 'Jakarta Selatan',
            'status' => 'active',
            'notes' => 'Aplikasi presensi geofencing anti fake-GPS karyawan lapangan.'
        ]);

        // 3. Sample Projects
        $prj1 = Project::updateOrCreate(['code' => 'PRJ-2026-01'], [
            'client_id' => $client1->id,
            'title' => 'POS Kasir & Multi-Outlet Inventory System',
            'category' => 'pos_erp',
            'status' => 'in_progress',
            'total_budget' => 18000000,
            'start_date' => '2026-09-01',
            'deadline' => '2026-10-15',
            'progress_percent' => 65,
            'description' => 'Sistem POS kasir 3 cabang terintegrasi printer thermal dan stok bahan baku resep otomatis.'
        ]);

        $prj2 = Project::updateOrCreate(['code' => 'PRJ-2026-02'], [
            'client_id' => $client2->id,
            'title' => 'Website Profil & Katalog Ekspor Bilingual',
            'category' => 'website',
            'status' => 'completed',
            'total_budget' => 9500000,
            'start_date' => '2026-08-10',
            'deadline' => '2026-09-10',
            'progress_percent' => 100,
            'description' => 'Landing page produk rempah kualitas ekspor dengan multi-language ID/EN dan WhatsApp Direct Order.'
        ]);

        $prj3 = Project::updateOrCreate(['code' => 'PRJ-2026-03'], [
            'client_id' => $client3->id,
            'title' => 'Mobile Presensi Karyawan GPS & Face Recognition',
            'category' => 'mobile_app',
            'status' => 'deal_dp',
            'total_budget' => 24000000,
            'start_date' => '2026-09-15',
            'deadline' => '2026-11-20',
            'progress_percent' => 25,
            'description' => 'Mobile app absensi karyawan dengan geofencing lokasi kerja, verifikasi wajah AI, dan live dashboard manager.'
        ]);

        // 4. Sample Invoices (Financial Inflows)
        Invoice::updateOrCreate(['invoice_number' => 'INV/2026/09/001'], [
            'project_id' => $prj1->id,
            'client_id' => $client1->id,
            'type' => 'dp_50',
            'amount' => 9000000,
            'status' => 'paid',
            'issue_date' => '2026-09-01',
            'due_date' => '2026-09-05',
            'paid_at' => '2026-09-02 14:30:00',
            'payment_method' => 'transfer_bca',
            'notes' => 'Pembayaran Uang Muka (DP 50%) proyek POS Kasir. Lunas via BCA.'
        ]);

        Invoice::updateOrCreate(['invoice_number' => 'INV/2026/09/002'], [
            'project_id' => $prj1->id,
            'client_id' => $client1->id,
            'type' => 'pelunasan',
            'amount' => 9000000,
            'status' => 'unpaid',
            'issue_date' => '2026-09-20',
            'due_date' => '2026-10-15',
            'payment_method' => 'transfer_bca',
            'notes' => 'Pelunasan sisa 50% setelah serah terima sistem & training staf.'
        ]);

        Invoice::updateOrCreate(['invoice_number' => 'INV/2026/08/003'], [
            'project_id' => $prj2->id,
            'client_id' => $client2->id,
            'type' => 'full_payment',
            'amount' => 9500000,
            'status' => 'paid',
            'issue_date' => '2026-08-10',
            'due_date' => '2026-08-15',
            'paid_at' => '2026-08-12 10:15:00',
            'payment_method' => 'transfer_mandiri',
            'notes' => 'Pembayaran penuh proyek website katalog ekspor.'
        ]);

        Invoice::updateOrCreate(['invoice_number' => 'INV/2026/09/004'], [
            'project_id' => $prj3->id,
            'client_id' => $client3->id,
            'type' => 'dp_50',
            'amount' => 12000000,
            'status' => 'paid',
            'issue_date' => '2026-09-15',
            'due_date' => '2026-09-18',
            'paid_at' => '2026-09-16 11:20:00',
            'payment_method' => 'transfer_bca',
            'notes' => 'DP 50% Mobile Presensi GPS.'
        ]);

        // 5. Sample Expenses (Financial Outflows)
        Expense::updateOrCreate(['title' => 'Sewa Hosting LiteSpeed & Domain ciptacoding.com'], [
            'category' => 'server_cloud',
            'amount' => 1250000,
            'expense_date' => '2026-09-05',
            'notes' => 'Perpanjangan paket hosting LiteSpeed idwebhost 1 tahun.'
        ]);

        Expense::updateOrCreate(['title' => 'Fee UI/UX Designer Freelance (Aplikasi Presensi)'], [
            'category' => 'freelance_salary',
            'amount' => 3500000,
            'expense_date' => '2026-09-18',
            'project_id' => $prj3->id,
            'notes' => 'Pengerjaan 18 screen desain Figma mobile app presensi.'
        ]);

        Expense::updateOrCreate(['title' => 'Langganan AI & Developer Tools'], [
            'category' => 'tools_licenses',
            'amount' => 650000,
            'expense_date' => '2026-09-10',
            'notes' => 'Langganan tools dev bulanan.'
        ]);

        // 6. Seed Portfolios from JSON
        $jsonPath = public_path('data/portofolio.json');
        if (file_exists($jsonPath)) {
            $data = json_decode(file_get_contents($jsonPath), true);
            if (is_array($data)) {
                foreach ($data as $p) {
                    Portfolio::updateOrCreate(['code' => $p['code']], [
                        'title' => $p['title'],
                        'categories' => $p['categories'] ?? ['bisnis'],
                        'category_label' => $p['categoryLabel'] ?? 'Sistem Digital',
                        'description' => $p['description'] ?? '',
                        'image' => $p['image'] ?? 'assets/images/pos-erp-multi-outlet.svg',
                        'badge_top' => $p['badgeTop'] ?? ['text' => 'Live Project', 'dot' => true],
                        'badge_bottom' => $p['badgeBottom'] ?? 'Digital Solution',
                        'tech_stack' => $p['techStack'] ?? [],
                        'highlight' => $p['highlight'] ?? 'Siap Pakai',
                        'highlight_icon' => $p['highlightIcon'] ?? 'check_circle',
                        'cta_text' => $p['ctaText'] ?? 'Tanya Spek',
                        'whatsapp_text' => $p['whatsappText'] ?? '',
                        'order' => $p['order'] ?? 0,
                    ]);
                }
            }
        }
    }
}
