<?php

namespace App\Http\Controllers;

use App\Models\InvoiceTemplate;
use Illuminate\View\View;

class InvoiceTemplateController extends Controller
{
    public function index(): View
    {
        $templates = InvoiceTemplate::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('invoice-templates.index', compact('templates'));
    }
}
