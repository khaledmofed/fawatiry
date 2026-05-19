<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMediaRequest;
use App\Models\Media;
use App\Services\MediaUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MediaController extends Controller
{
    public function __construct(
        private MediaUploadService $mediaUploadService
    ) {}

    public function index(): View
    {
        $media = Media::query()->latest()->paginate(24);

        return view('media.index', compact('media'));
    }

    public function store(StoreMediaRequest $request): RedirectResponse
    {
        $this->mediaUploadService->store($request->file('file'), $request->user());

        return redirect()->route('media.index')->with('success', __('File uploaded.'));
    }

    public function destroy(Media $medium): RedirectResponse
    {
        $this->mediaUploadService->delete($medium);

        return redirect()->route('media.index')->with('success', __('File removed.'));
    }
}
