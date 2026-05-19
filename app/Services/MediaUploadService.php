<?php

namespace App\Services;

use App\Models\Media;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MediaUploadService
{
    public function store(UploadedFile $file, ?User $user): Media
    {
        $path = $file->store('media', 'public');

        return Media::query()->create([
            'user_id' => $user?->id,
            'disk' => 'public',
            'path' => $path,
            'mime' => $file->getMimeType(),
            'size' => $file->getSize(),
            'original_name' => $file->getClientOriginalName(),
        ]);
    }

    public function delete(Media $media): void
    {
        if (Storage::disk($media->disk)->exists($media->path)) {
            Storage::disk($media->disk)->delete($media->path);
        }
        $media->delete();
    }
}
