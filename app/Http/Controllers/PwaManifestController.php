<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class PwaManifestController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $startUrl = rtrim(url('/'), '/').'/';
        $scope = $startUrl;
        $id = parse_url($startUrl, PHP_URL_PATH) ?: '/';

        return response()->json([
            'id' => $id,
            'name' => 'Construtec Obras',
            'short_name' => 'Construtec',
            'description' => 'Sistema de gestão e acompanhamento diário de obras.',
            'start_url' => $startUrl,
            'scope' => $scope,
            'display' => 'standalone',
            'background_color' => '#0f172a',
            'theme_color' => '#0f172a',
            'orientation' => 'portrait',
            'icons' => [
                [
                    'src' => asset('android-chrome-192x192.png'),
                    'sizes' => '192x192',
                    'type' => 'image/png',
                    'purpose' => 'any',
                ],
                [
                    'src' => asset('android-chrome-512x512.png'),
                    'sizes' => '512x512',
                    'type' => 'image/png',
                    'purpose' => 'any',
                ],
                [
                    'src' => asset('android-chrome-192x192.png'),
                    'sizes' => '192x192',
                    'type' => 'image/png',
                    'purpose' => 'maskable',
                ],
                [
                    'src' => asset('android-chrome-512x512.png'),
                    'sizes' => '512x512',
                    'type' => 'image/png',
                    'purpose' => 'maskable',
                ],
            ],
            'categories' => ['business', 'productivity'],
        ], 200, [
            'Content-Type' => 'application/manifest+json',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ]);
    }
}
