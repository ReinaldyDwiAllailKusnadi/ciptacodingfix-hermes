<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Portfolio;
use Illuminate\Support\Facades\File;

class PortfolioController extends Controller
{
    public function index()
    {
        $portfolios = Portfolio::orderBy('order')->orderBy('id')->get();
        return Inertia::render('Portfolio/Index', [
            'portfolios' => $portfolios,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'code' => 'required|string|unique:portfolios,code',
            'category_label' => 'nullable|string',
            'categories' => 'required|array',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'badge_top' => 'nullable|array',
            'badge_bottom' => 'nullable|string',
            'tech_stack' => 'nullable|array',
            'highlight' => 'nullable|string',
            'cta_text' => 'nullable|string',
            'whatsapp_text' => 'nullable|string',
        ]);

        $maxOrder = Portfolio::max('order') ?: 0;
        $validated['order'] = $maxOrder + 1;
        $validated['image'] = $validated['image'] ?: 'assets/images/pos-erp-multi-outlet.svg';

        Portfolio::create($validated);
        $this->syncToJson();

        return redirect()->back()->with('success', 'Katalog portofolio berhasil ditambahkan!');
    }

    public function update(Request $request, Portfolio $portfolio)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'code' => 'required|string|unique:portfolios,code,' . $portfolio->id,
            'category_label' => 'nullable|string',
            'categories' => 'required|array',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'badge_top' => 'nullable|array',
            'badge_bottom' => 'nullable|string',
            'tech_stack' => 'nullable|array',
            'highlight' => 'nullable|string',
            'cta_text' => 'nullable|string',
            'whatsapp_text' => 'nullable|string',
        ]);

        $portfolio->update($validated);
        $this->syncToJson();

        return redirect()->back()->with('success', 'Katalog portofolio berhasil diperbarui!');
    }

    public function destroy(Portfolio $portfolio)
    {
        $portfolio->delete();
        $this->syncToJson();

        return redirect()->back()->with('success', 'Portofolio berhasil dihapus.');
    }

    private function syncToJson()
    {
        $items = Portfolio::orderBy('order')->get()->map(function ($p) {
            return [
                'id' => $p->id,
                'code' => $p->code,
                'title' => $p->title,
                'categories' => $p->categories,
                'categoryLabel' => $p->category_label,
                'description' => $p->description,
                'image' => $p->image,
                'badgeTop' => $p->badge_top,
                'badgeBottom' => $p->badge_bottom,
                'techStack' => $p->tech_stack,
                'highlight' => $p->highlight,
                'highlightIcon' => $p->highlight_icon ?: 'check_circle',
                'ctaText' => $p->cta_text ?: 'Tanya Spek',
                'whatsappText' => $p->whatsapp_text,
                'order' => $p->order,
            ];
        });

        $path = public_path('data/portofolio.json');
        File::put($path, json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }
}
