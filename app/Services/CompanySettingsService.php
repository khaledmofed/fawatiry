<?php

namespace App\Services;

use App\Models\CompanySetting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CompanySettingsService
{
    public function get(): CompanySetting
    {
        return CompanySetting::current();
    }

    public function replaceLogo(UploadedFile $logo): CompanySetting
    {
        $settings = CompanySetting::current();
        $this->deleteIfExists($settings->logo_path);
        $settings->logo_path = $logo->store('company', 'public');
        $settings->save();

        return $settings;
    }

    public function update(array $data, ?UploadedFile $logo, ?UploadedFile $signature, ?UploadedFile $stamp): CompanySetting
    {
        $settings = CompanySetting::current();

        if ($logo) {
            $this->deleteIfExists($settings->logo_path);
            $settings->logo_path = $logo->store('company', 'public');
        }
        if ($signature) {
            $this->deleteIfExists($settings->signature_path);
            $settings->signature_path = $signature->store('company', 'public');
        }
        if ($stamp) {
            $this->deleteIfExists($settings->stamp_path);
            $settings->stamp_path = $stamp->store('company', 'public');
        }

        $settings->fill($data);
        $settings->save();

        return $settings;
    }

    private function deleteIfExists(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
