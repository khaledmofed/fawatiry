<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateCompanySettingsRequest;
use App\Services\CompanySettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function __construct(
        private CompanySettingsService $companySettingsService
    ) {}

    public function edit(): View
    {
        $settings = $this->companySettingsService->get();

        return view('settings.edit', compact('settings'));
    }

    public function update(UpdateCompanySettingsRequest $request): RedirectResponse
    {
        $data = $request->safe()->only([
            'company_name',
            'address',
            'vat_number',
            'phone',
            'email',
            'default_currency',
            'default_tax_rate',
        ]);
        $this->companySettingsService->update(
            $data,
            $request->file('logo'),
            $request->file('signature'),
            $request->file('stamp'),
        );

        return redirect()->route('settings.edit')->with('success', __('Settings saved.'));
    }
}
