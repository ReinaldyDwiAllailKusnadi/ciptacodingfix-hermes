<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Clients Table (CRM)
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('company')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->enum('status', ['active', 'prospect', 'inactive'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 2. Projects Table
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->string('code')->unique(); // e.g. PRJ-2026-01
            $table->string('title');
            $table->string('category')->default('website'); // website, pos_erp, mobile_app, custom_software, it_consulting
            $table->enum('status', ['lead', 'deal_dp', 'in_progress', 'review', 'completed', 'cancelled'])->default('lead');
            $table->unsignedBigInteger('total_budget')->default(0);
            $table->date('start_date')->nullable();
            $table->date('deadline')->nullable();
            $table->unsignedTinyInteger('progress_percent')->default(0); // 0 - 100
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 3. Invoices Table (Financial Management)
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('set null');
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->string('invoice_number')->unique(); // e.g. INV/2026/09/001
            $table->string('type')->default('dp_50'); // dp_50, termin_1, termin_2, pelunasan, full_payment
            $table->unsignedBigInteger('amount');
            $table->enum('status', ['unpaid', 'paid', 'overdue', 'cancelled'])->default('unpaid');
            $table->date('issue_date');
            $table->date('due_date');
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_method')->nullable(); // transfer_bca, transfer_mandiri, qris, cash
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 4. Expenses Table (Financial Cash Out)
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('category', ['server_cloud', 'freelance_salary', 'tools_licenses', 'operational', 'marketing'])->default('operational');
            $table->unsignedBigInteger('amount');
            $table->date('expense_date');
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('set null');
            $table->string('receipt_url')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 5. Portfolios Table (Public Showcase CMS)
        Schema::create('portfolios', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('title');
            $table->json('categories')->nullable();
            $table->string('category_label')->nullable();
            $table->text('description')->nullable();
            $table->string('image')->default('assets/images/pos-erp-multi-outlet.svg');
            $table->json('badge_top')->nullable();
            $table->string('badge_bottom')->nullable();
            $table->json('tech_stack')->nullable();
            $table->string('highlight')->nullable();
            $table->string('highlight_icon')->default('check_circle');
            $table->string('cta_text')->default('Tanya Spek');
            $table->string('whatsapp_text')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolios');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('clients');
    }
};
