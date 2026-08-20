<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $images = $this->siteImages();

        $hero = $this->pick($images, 'IMG_0442.jpg')
            ?? $this->pick($images, 'IMG_7250.jpg')
            ?? $images->first();

        $about = $this->pick($images, 'IMG_4442.jpg')
            ?? $this->pick($images, 'IMG_9142.jpg')
            ?? $images->skip(1)->first();

        $gallery = $images
            ->reject(fn (string $path) => in_array($path, array_filter([$hero, $about]), true))
            ->values();

        $contact = [
            'phone' => config('construtec.phone'),
            'whatsapp' => config('construtec.whatsapp'),
            'instagram' => config('construtec.instagram'),
            'email' => config('construtec.email'),
            'city' => config('construtec.city'),
        ];

        return view('home', compact('hero', 'about', 'gallery', 'contact'));
    }

    /**
     * @return \Illuminate\Support\Collection<int, string>
     */
    private function siteImages()
    {
        $directory = public_path('images/site');

        if (! is_dir($directory)) {
            return collect();
        }

        return collect(File::files($directory))
            ->filter(fn ($file) => in_array(strtolower($file->getExtension()), ['jpg', 'jpeg', 'png', 'webp'], true))
            ->sortBy(fn ($file) => $file->getFilename())
            ->map(fn ($file) => 'images/site/'.$file->getFilename())
            ->values();
    }

    private function pick($images, string $filename): ?string
    {
        $path = 'images/site/'.$filename;

        return $images->contains($path) ? $path : null;
    }
}
