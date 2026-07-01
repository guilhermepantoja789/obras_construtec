@php
    $viteManifest = public_path('build/manifest.json');
@endphp
@if (file_exists($viteManifest))
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@else
    {{-- Fallback se o build do deploy não existir (evita 500 em todas as páginas) --}}
    <script src="https://cdn.tailwindcss.com"></script>
@endif
