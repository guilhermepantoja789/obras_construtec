<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Diário de Obras') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @include('partials.vite-assets')

        <style>
            .turbo-progress-bar {
                height: 3px;
                background-color: #f59e0b; /* bg-amber-500 */
                z-index: 9999;
            }
        </style>

        <!-- PWA -->
        <link rel="manifest" href="{{ route('pwa.manifest') }}">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="Construtec">
        <meta name="theme-color" content="#0f172a">
        <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
        
        <script>
            // Gerador Automático de Splash Screen para iOS
            (function() {
                const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
                if (!isIOS) return;

                // Não re-gerar se já estiver rodando standalone
                if (window.matchMedia('(display-mode: standalone)').matches || navigator.standalone) {
                    // Mas precisamos do link no head? O iOS lê isso na hora do 'Add to Home Screen' ou ao abrir.
                    // Para garantir, vamos adicionar.
                }

                function setupSplash() {
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');
                    const width = window.screen.width * window.devicePixelRatio;
                    const height = window.screen.height * window.devicePixelRatio;

                    canvas.width = width;
                    canvas.height = height;

                    // Cor de fundo do app (Slate 900)
                    ctx.fillStyle = '#0f172a';
                    ctx.fillRect(0, 0, width, height);

                    // Padrão de textura stardust (opcional, ou podemos deixar apenas sólido que fica mais limpo e rápido)

                    // Ícone/Texto Centralizado
                    ctx.fillStyle = '#ffffff';
                    ctx.font = 'bold ' + (40 * window.devicePixelRatio) + 'px "Inter", sans-serif';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';

                    // Tentaremos carregar o ícone se quisermos
                    const logoImg = new Image();
                    logoImg.onload = function() {
                        const imgSize = 120 * window.devicePixelRatio;
                        ctx.drawImage(logoImg, (width - imgSize) / 2, (height - imgSize) / 2 - (30 * window.devicePixelRatio), imgSize, imgSize);

                        ctx.fillText('DIÁRIO DE OBRAS', width / 2, height / 2 + (60 * window.devicePixelRatio));

                        const dataURL = canvas.toDataURL('image/png');
                        const link = document.createElement('link');
                        link.setAttribute('rel', 'apple-touch-startup-image');
                        link.setAttribute('media', '(device-width: ' + window.screen.width + 'px) and (device-height: ' + window.screen.height + 'px) and (-webkit-device-pixel-ratio: ' + window.devicePixelRatio + ')');
                        link.setAttribute('href', dataURL);
                        document.head.appendChild(link);
                    };
                    logoImg.src = "{{ asset('icon.png') }}"; // Certifique-se de que icon.png é a logo quadrada.

                    // Fallback se a imagem falhar/demorar muito (só texto)
                    setTimeout(() => {
                        if (!document.querySelector('link[rel="apple-touch-startup-image"]')) {
                            ctx.fillText('DIÁRIO DE OBRAS', width / 2, height / 2);
                            const dataURL = canvas.toDataURL('image/png');
                            const link = document.createElement('link');
                            link.setAttribute('rel', 'apple-touch-startup-image');
                            link.setAttribute('href', dataURL);
                            document.head.appendChild(link);
                        }
                    }, 500);
                }

                if (document.readyState === 'complete') {
                    setupSplash();
                } else {
                    window.addEventListener('load', setupSplash);
                }
            })();

            window.deferredPrompt = null;

            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register("{{ asset('app/sw.js') }}", { scope: "{{ url('/app') }}/" }).then(registration => {
                        console.log('Service Worker registrado com escopo:', registration.scope);

                        // Detect update
                        registration.onupdatefound = () => {
                            const installingWorker = registration.installing;
                            installingWorker.onstatechange = () => {
                                if (installingWorker.state === 'installed') {
                                    if (navigator.serviceWorker.controller) {
                                        // New update available
                                        window.dispatchEvent(new CustomEvent('pwa-update-available', { detail: registration }));
                                    }
                                }
                            };
                        };
                    }).catch(error => {
                        console.error('Falha ao registrar o Service Worker:', error);
                    });
                });

                // Listen for sync-success message from Service Worker
                navigator.serviceWorker.addEventListener('message', (event) => {
                    if (event.data?.type === 'sync-success') {
                        if (typeof showToast === 'function') {
                            showToast('✓ Post sincronizado com sucesso!');
                        }
                        if (typeof updatePendingBadge === 'function') {
                            updatePendingBadge();
                        }
                    }
                });
            }

            // Android: capture install prompt
            window.addEventListener('beforeinstallprompt', (e) => {
                e.preventDefault();
                window.deferredPrompt = e;
                window.dispatchEvent(new CustomEvent('pwa-install-available'));
            });

            window.addEventListener('appinstalled', () => {
                window.deferredPrompt = null;
                console.log('PWA instalado com sucesso');
            });

            function pwaManager() {
                return {
                    showAndroidBanner: false,
                    showIosGuide: false,
                    showUpdateToast: false,
                    isIOS: /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream,
                    isStandalone: window.matchMedia('(display-mode: standalone)').matches || navigator.standalone,
                    registration: null,
                    init() {
                        if (window.deferredPrompt && !this.isStandalone) {
                            this.showAndroidBanner = true;
                        }

                        window.addEventListener('pwa-install-available', () => {
                            if (!this.isStandalone) this.showAndroidBanner = true;
                        });

                        window.addEventListener('pwa-update-available', (e) => {
                            this.registration = e.detail;
                            this.showUpdateToast = true;
                        });
                        
                        if (this.isIOS && !this.isStandalone && !localStorage.getItem('pwa_ios_guide_dismissed')) {
                            setTimeout(() => this.showIosGuide = true, 3000);
                        }
                    }
                };
            }
        </script>
    </head>
    <body class="font-sans text-slate-200 antialiased bg-slate-900 bg-[url('https://www.transparenttextures.com/patterns/stardust.png')] relative overflow-x-hidden">
        <div class="fixed inset-0 bg-gradient-to-br from-slate-900 via-slate-900/95 to-slate-800 -z-10"></div>
        
        <div class="min-h-screen pb-20 sm:pb-0 safe-area-bottom" x-data="{ showGeneralMenu: false, showCreatePostModal: false, showActionMenu: false, showNotaModal: false }">

            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-slate-800/50 backdrop-blur-md border-b border-white/10 shadow-lg sticky top-0 sm:top-16 z-40">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            @if(session('success'))
                <div class="fixed top-20 right-4 z-[100] p-4 bg-green-500/20 border border-green-500/50 rounded-2xl text-green-500 text-sm backdrop-blur-xl shadow-2xl" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="fixed top-20 right-4 z-[100] p-4 bg-red-500/20 border border-red-500/50 rounded-2xl text-red-500 text-sm backdrop-blur-xl shadow-2xl" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
                    {{ session('error') }}
                </div>
            @endif

            <x-validation-errors />

            <!-- Page Content -->
            <main>
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    {{ $slot }}
                </div>
            </main>
 
            {{ $modals ?? '' }}

            <!-- Quick Post Modal -->
            <div x-show="showCreatePostModal" 
                 class="fixed inset-0 z-[100] flex items-center justify-center px-4"
                 style="display: none;">
                <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-md" @click="showCreatePostModal = false"></div>
                
                <!-- Modal Panel -->
                <div @click.stop class="relative w-full max-w-lg bg-slate-900 rounded-3xl shadow-2xl overflow-hidden border border-white/10" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="scale-95 opacity-0" x-transition:enter-end="scale-100 opacity-100">
                    <!-- Modal Header -->
                    <div class="flex justify-between items-center p-6 border-b border-white/10">
                        <div>
                            <p class="text-[10px] font-black text-amber-500 uppercase tracking-[0.2em] mb-1">Nova Entrada</p>
                            <h3 class="font-black text-lg text-white uppercase tracking-tight">Registro do Dia</h3>
                        </div>
                        <button @click="showCreatePostModal = false" class="p-2 text-slate-500 hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    @if($diaEncerrado)
                        <div class="p-12 text-center">
                            <div class="w-16 h-16 bg-green-500/10 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <p class="text-white font-black uppercase text-sm mb-2">Diário Encerrado</p>
                            <p class="text-slate-400 text-xs">O relatório oficial deste dia já foi emitido. Novas publicações não são permitidas.</p>
                        </div>
                    @else
                    
                    <form id="diario-post-form" action="{{ route('diario-posts.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6" x-data="{ photoName: null, photoPreview: null, photoFile: null, submitting: false, queued: false }">
                        @csrf
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">O que aconteceu na obra hoje?</label>
                            <textarea id="post-texto" name="texto" rows="4" class="w-full bg-white/5 border-white/10 rounded-2xl text-white placeholder-slate-600 focus:border-amber-500 focus:ring-amber-500" placeholder="Descreva brevemente o progresso, intercorrências ou avisos..."></textarea>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Registros Fotográficos <span class="text-amber-500">*</span></label>
                            
                            <div x-show="photoPreview" class="relative w-full aspect-video rounded-2xl overflow-hidden border border-white/10 mb-4" style="display: none;">
                                <img :src="photoPreview" class="w-full h-full object-cover">
                                <button type="button" @click="photoPreview = null; photoName = null; photoFile = null" class="absolute top-2 right-2 p-1 bg-red-500 rounded-full text-white shadow-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>

                            <div class="relative group" x-show="!photoPreview">
                                <input type="file" id="post-foto" name="foto" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                       @change="
                                            photoFile = $event.target.files[0];
                                            photoName = photoFile.name;
                                            const reader = new FileReader();
                                            reader.onload = (e) => { photoPreview = e.target.result; };
                                            reader.readAsDataURL(photoFile);
                                       ">
                                <div class="p-8 border-2 border-dashed border-white/10 rounded-2xl flex flex-col items-center gap-3 group-hover:border-amber-500/50 transition-all bg-white/[0.02]">
                                    <svg class="w-10 h-10 text-slate-600 group-hover:text-amber-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <p class="text-xs text-slate-500">Toque para tirar foto ou escolher da galeria</p>
                                </div>
                            </div>
                            
                            <div x-show="photoName" class="text-[10px] text-amber-500 font-bold uppercase tracking-widest mt-2">
                                Arquivo: <span x-text="photoName"></span>
                            </div>
                        </div>

                        <!-- Offline queued banner -->
                        <div x-show="queued" class="p-3 bg-amber-500/10 border border-amber-500/30 rounded-2xl text-amber-400 text-xs font-bold flex items-center gap-2" style="display:none;">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Salvo localmente. Será enviado quando a conexão voltar.
                        </div>

                        <button type="button" @click="submitPost($event, $data)" :disabled="submitting || queued"
                            class="w-full py-4 bg-amber-500 hover:bg-amber-400 text-slate-900 font-black rounded-2xl transition-all shadow-lg shadow-amber-500/20 uppercase tracking-widest text-xs disabled:opacity-60">
                            <span x-show="!submitting && !queued">Publicar no Diário</span>
                            <span x-show="submitting">Enviando...</span>
                            <span x-show="queued">✓ Na fila para sincronizar</span>
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            <!-- Modal Registrar Nota Fiscal -->
            <div x-show="showNotaModal" 
                 class="fixed inset-0 z-[100] flex items-center justify-center px-4"
                 style="display: none;">
                <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-md" @click="showNotaModal = false"></div>
                
                <div @click.stop class="relative w-full max-w-lg bg-slate-900 rounded-3xl shadow-2xl overflow-hidden border border-white/10" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="scale-95 opacity-0" x-transition:enter-end="scale-100 opacity-100">
                    <div class="flex justify-between items-center p-6 border-b border-white/10">
                        <div>
                            <p class="text-[10px] font-black text-indigo-500 uppercase tracking-[0.2em] mb-1">Financeiro</p>
                            <h3 class="font-black text-lg text-white uppercase tracking-tight">Registrar Nota</h3>
                        </div>
                        <button @click="showNotaModal = false" class="p-2 text-slate-500 hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <form action="{{ route('nota-fiscals.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                        @csrf
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest block mb-1">Nº da Nota</label>
                                <input type="text" name="numero_nota" required class="w-full bg-white/5 border-white/10 rounded-xl text-white text-sm focus:border-indigo-500">
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest block mb-1">Valor (R$)</label>
                                <input type="number" step="0.01" name="valor" required class="w-full bg-white/5 border-white/10 rounded-xl text-white text-sm focus:border-indigo-500">
                            </div>
                        </div>

                        <div>
                            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest block mb-1">Descrição</label>
                            <input type="text" name="descricao" required placeholder="Ex: Cimento, Tijolos..." class="w-full bg-white/5 border-white/10 rounded-xl text-white text-sm focus:border-indigo-500">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest block mb-1">Data</label>
                                <input type="date" name="data_recebimento" required value="{{ date('Y-m-d') }}" class="w-full bg-white/5 border-white/10 rounded-xl text-white text-sm">
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest block mb-1">Recebido por</label>
                                <input type="text" name="quem_recebeu" required value="{{ auth()->user()->name }}" class="w-full bg-white/5 border-white/10 rounded-xl text-white text-sm focus:border-indigo-500">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest block">Arquivo ou Foto da Nota</label>
                            <div class="relative group" x-data="{ hasFile: false }">
                                <input type="file" name="arquivo" accept="image/*,application/pdf" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" @change="hasFile = $event.target.files.length > 0">
                                <div class="p-4 border-2 border-dashed border-white/10 rounded-2xl flex items-center justify-center gap-3 transition-all bg-white/[0.02]" :class="hasFile ? 'border-indigo-500/50 bg-indigo-500/5' : 'group-hover:border-indigo-500/30'">
                                    <svg class="w-6 h-6 text-slate-500" :class="hasFile ? 'text-indigo-500' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <p class="text-[10px] font-bold uppercase tracking-widest" :class="hasFile ? 'text-indigo-500' : 'text-slate-500'" x-text="hasFile ? 'Arquivo Selecionado ✓' : 'Tirar Foto ou PDF'"></p>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="w-full py-4 bg-indigo-600 hover:bg-indigo-500 text-white font-black rounded-2xl transition-all shadow-lg shadow-indigo-600/20 uppercase tracking-widest text-xs">
                            Salvar Nota
                        </button>
                    </form>
                </div>
            </div>


            <!-- General Menu Overlay (Slide up) -->
            <div x-show="showGeneralMenu" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="translate-y-full opacity-0"
                 x-transition:enter-end="translate-y-0 opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="translate-y-0 opacity-100"
                 x-transition:leave-end="translate-y-full opacity-0"
                 class="fixed inset-0 z-[60] flex items-end sm:items-center justify-center px-4 pb-24 sm:pb-0 safe-area-bottom"
                 style="display: none;">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showGeneralMenu = false"></div>
                
                <div class="relative w-full max-w-sm bg-slate-800/90 backdrop-blur-2xl border border-white/10 rounded-3xl shadow-2xl p-6 overflow-hidden">
                    <div class="grid grid-cols-3 gap-6">
                        <!-- Propostas -->
                        @php $activeObraId = session('active_obra_id'); @endphp
                        @if(Auth::user()->isChefe())
                        <a href="{{ route('propostas.index') }}" class="flex flex-col items-center gap-2 group">
                            <div class="w-12 h-12 bg-amber-500/10 rounded-2xl flex items-center justify-center text-amber-500 border border-amber-500/20 group-active:scale-90 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <span class="text-[10px] font-bold text-slate-300 uppercase tracking-tighter">Propostas</span>
                        </a>
                        @elseif(Auth::user()->isClient())
                        <a href="{{ $clientePropostaId ? route('propostas.cliente.show', $clientePropostaId) : '#' }}" class="flex flex-col items-center gap-2 group {{ !$clientePropostaId ? 'opacity-50 pointer-events-none' : '' }}">
                            <div class="w-12 h-12 bg-amber-500/10 rounded-2xl flex items-center justify-center text-amber-500 border border-amber-500/20 group-active:scale-90 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <span class="text-[10px] font-bold text-slate-300 uppercase tracking-tighter">Propostas</span>
                        </a>
                        @endif

                        <!-- Diários (Histórico) -->
                        <a href="{{ $activeObraId ? route('diario-reports.index') : '#' }}" class="flex flex-col items-center gap-2 group {{ !$activeObraId ? 'opacity-50' : '' }}">
                            <div class="w-12 h-12 bg-cyan-500/10 rounded-2xl flex items-center justify-center text-cyan-500 border border-cyan-500/20 group-active:scale-90 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <span class="text-[10px] font-bold text-slate-300 uppercase tracking-tighter">Diários</span>
                        </a>

                        <!-- Cronograma -->
                        <a href="{{ route('etapa-obras.index') }}" class="flex flex-col items-center gap-2 group">
                            <div class="w-12 h-12 bg-blue-500/10 rounded-2xl flex items-center justify-center text-blue-500 border border-blue-500/20 group-active:scale-90 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                            <span class="text-[10px] font-bold text-slate-300 uppercase tracking-tighter">Cronograma</span>
                        </a>

                        <!-- Financeiro -->
                        @if(Auth::user()->isChefe())
                        <a href="{{ route('financeiro.index') }}" class="flex flex-col items-center gap-2 group">
                            <div class="w-12 h-12 bg-green-500/10 rounded-2xl flex items-center justify-center text-green-500 border border-green-500/20 group-active:scale-90 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <span class="text-[10px] font-bold text-slate-300 uppercase tracking-tighter">Financeiro</span>
                        </a>
                        @endif

                        <!-- Empreiteiras -->
                        @if(Auth::user()->isChefe())
                        <a href="{{ route('empreiteiras.index') }}" class="flex flex-col items-center gap-2 group">
                            <div class="w-12 h-12 bg-orange-500/10 rounded-2xl flex items-center justify-center text-orange-500 border border-orange-500/20 group-active:scale-90 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <span class="text-[10px] font-bold text-slate-300 uppercase tracking-tighter">Empreiteiras</span>
                        </a>
                        @endif

                        <!-- Contrato -->
                        @if(Auth::user()->isChefe())
                        <a href="{{ $activeObraId ? route('contrato.edit', $activeObraId) : '#' }}" class="flex flex-col items-center gap-2 group {{ !$activeObraId ? 'opacity-50' : '' }}">
                            <div class="w-12 h-12 bg-amber-500/10 rounded-2xl flex items-center justify-center text-amber-500 border border-amber-500/20 group-active:scale-90 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <span class="text-[10px] font-bold text-slate-300 uppercase tracking-tighter">Contrato</span>
                        </a>
                        @endif

                        <!-- Equipe -->
                        @if(Auth::user()->isChefe())
                        <a href="{{ route('users.index') }}" class="flex flex-col items-center gap-2 group">
                            <div class="w-12 h-12 bg-rose-500/10 rounded-2xl flex items-center justify-center text-rose-500 border border-rose-500/20 group-active:scale-90 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                            </div>
                            <span class="text-[10px] font-bold text-slate-300 uppercase tracking-tighter">Equipe</span>
                        </a>
                        @endif

                        <!-- Notas Fiscais -->
                        @if(!Auth::user()->isClient())
                        <a href="{{ route('nota-fiscals.index') }}" class="flex flex-col items-center gap-2 group">
                            <div class="w-12 h-12 bg-indigo-500/10 rounded-2xl flex items-center justify-center text-indigo-500 border border-indigo-500/20 group-active:scale-90 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <span class="text-[10px] font-bold text-slate-300 uppercase tracking-tighter">Notas Fiscais</span>
                        </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Bottom Navigation (Mobile Only) -->
            <nav class="sm:hidden fixed bottom-0 left-0 right-0 bg-slate-900/90 backdrop-blur-xl border-t border-white/10 px-6 py-3 z-50 flex justify-between items-center safe-area-bottom">
                @php
                    $obraCountBottom = isset($availableObras) ? $availableObras->count() : 0;
                    $showObrasMenuBottom = !Auth::user()->isClient() || $obraCountBottom > 1;
                @endphp
                <a href="{{ route('dashboard') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('dashboard') ? 'text-amber-500' : 'text-slate-400' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span class="text-[10px] font-bold uppercase tracking-wider">Home</span>
                </a>

                @if($showObrasMenuBottom)
                    <a href="{{ route('obras.index') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('obras.*') ? 'text-amber-500' : 'text-slate-400' }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <span class="text-[10px] font-bold uppercase tracking-wider">Obras</span>
                    </a>
                @endif

                <!-- Central Action Button Menu -->
                @if(!Auth::user()->isClient())
                <div class="-mt-12 relative" x-data="{ }">
                    <!-- Action Menu Options -->
                    <div x-show="showActionMenu" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-4 scale-95"
                         x-transition:enter-end="opacity-100 -translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 -translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 -translate-y-4 scale-95"
                         class="absolute bottom-20 left-1/2 -translate-x-1/2 w-48 space-y-3 z-50"
                         style="display: none;">
                        
                        <!-- Opção 1: Diário -->
                        <button @click="showActionMenu = false; showCreatePostModal = true" class="w-full bg-slate-800/95 backdrop-blur-xl border border-white/10 p-4 rounded-2xl flex items-center gap-3 shadow-2xl active:scale-95 transition-all">
                            <div class="w-8 h-8 bg-amber-500 rounded-xl flex items-center justify-center text-slate-900 shadow-lg shadow-amber-500/20">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            </div>
                            <span class="text-[10px] font-black text-white uppercase tracking-widest text-left">Diário de Obra</span>
                        </button>

                        <!-- Opção 2: Nota Fiscal -->
                        @if(!Auth::user()->isClient())
                        <button @click="showActionMenu = false; showNotaModal = true" class="w-full bg-slate-800/95 backdrop-blur-xl border border-white/10 p-4 rounded-2xl flex items-center gap-3 shadow-2xl active:scale-95 transition-all">
                            <div class="w-8 h-8 bg-indigo-500 rounded-xl flex items-center justify-center text-white shadow-lg shadow-indigo-500/20">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <span class="text-[10px] font-black text-white uppercase tracking-widest text-left">Registrar Nota</span>
                        </button>
                        @endif
                    </div>

                    @if($diaEncerrado)
                        <div class="w-14 h-14 bg-slate-700 rounded-full flex items-center justify-center shadow-lg border-4 border-slate-900 text-slate-500 cursor-not-allowed" title="Diário encerrado">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                    @else
                        <button @click="showActionMenu = !showActionMenu" class="w-14 h-14 rounded-full flex items-center justify-center shadow-lg border-4 border-slate-900 active:scale-90 transition-all z-[60] relative"
                                :class="showActionMenu ? 'bg-rose-500 text-white rotate-45' : 'bg-amber-500 text-slate-900'">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                        </button>
                    @endif

                    <!-- Pending offline posts badge -->
                    <span id="pending-posts-badge" style="display:none;" class="absolute -top-1 -right-1 w-5 h-5 bg-rose-500 rounded-full text-white text-[9px] font-black flex items-center justify-center shadow-lg z-10 leading-none"></span>
                </div>
                @endif

                <a href="{{ route('feed.index') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('feed.*') ? 'text-amber-500' : 'text-slate-400' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    <span class="text-[10px] font-bold uppercase tracking-wider">Diário</span>
                </a>

                <button @click="showGeneralMenu = !showGeneralMenu" 
                        class="flex flex-col items-center gap-1 transition-colors"
                        :class="showGeneralMenu ? 'text-amber-500' : 'text-slate-400'">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                    </svg>
                    <span class="text-[10px] font-bold uppercase tracking-wider">Geral</span>
                </button>
            </nav>

            <!-- PWA Installation & Update UI (Alpine.js) -->
            <div x-data="pwaManager()"
                class="fixed inset-0 pointer-events-none z-[110]"
                x-cloak>
                
                <!-- Android Install Banner -->
                <div x-show="showAndroidBanner" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="translate-y-full opacity-0"
                     x-transition:enter-end="translate-y-0 opacity-100"
                     class="absolute bottom-24 left-4 right-4 pointer-events-auto">
                    <div class="bg-slate-800/95 backdrop-blur-2xl border border-amber-500/30 rounded-3xl p-5 shadow-2xl flex items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-amber-500 rounded-2xl flex items-center justify-center shadow-lg">
                                <img src="{{ asset('icon.png') }}" class="w-8 h-8 rounded-lg" alt="App Icon">
                            </div>
                            <div>
                                <h4 class="text-white font-black text-sm uppercase tracking-tight">Instalar Diário</h4>
                                <p class="text-slate-400 text-[10px] uppercase font-bold tracking-widest">Acesso rápido e offline</p>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button @click="showAndroidBanner = false" class="px-4 py-2 text-slate-400 text-[10px] font-black uppercase tracking-widest">Agora não</button>
                            <button @click="
                                if (window.deferredPrompt) {
                                    window.deferredPrompt.prompt();
                                    window.deferredPrompt.userChoice.then((choice) => {
                                        window.deferredPrompt = null;
                                        showAndroidBanner = false;
                                    });
                                } else {
                                    showAndroidBanner = false;
                                }
                            " class="px-6 py-2 bg-amber-500 text-slate-900 text-[10px] font-black uppercase tracking-widest rounded-xl shadow-lg shadow-amber-500/20">Instalar</button>
                        </div>
                    </div>
                </div>

                <!-- iOS Install Guide -->
                <div x-show="showIosGuide" 
                     class="absolute inset-0 flex items-end justify-center pointer-events-auto bg-slate-950/40 backdrop-blur-sm"
                     style="display:none;">
                    <div @click.away="showIosGuide = false" class="w-full max-w-sm bg-slate-900 rounded-t-[40px] p-8 border-t border-white/10 shadow-2xl relative">
                        <div class="w-12 h-1.5 bg-white/10 rounded-full mx-auto mb-8"></div>
                        
                        <div class="flex flex-col items-center text-center space-y-6">
                            <div class="w-20 h-20 bg-amber-500 rounded-[32px] flex items-center justify-center shadow-2xl mb-2">
                                <img src="{{ asset('icon.png') }}" class="w-14 h-14 rounded-2xl" alt="App Icon">
                            </div>
                            
                            <div>
                                <h4 class="text-white font-black text-xl uppercase tracking-tighter mb-2">Instalar no iPhone</h4>
                                <p class="text-slate-400 text-sm leading-relaxed">Adicione o Diário à sua tela de início para uma experiência completa.</p>
                            </div>

                            <div class="w-full bg-white/5 rounded-3xl p-6 space-y-4 text-left">
                                <div class="flex items-center gap-4">
                                    <div class="w-8 h-8 bg-white/10 rounded-xl flex items-center justify-center text-white">1</div>
                                    <p class="text-slate-200 text-xs">Toque no ícone de <span class="text-amber-500 font-bold">Compartilhar</span> na barra inferior.</p>
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="w-8 h-8 bg-white/10 rounded-xl flex items-center justify-center text-white">2</div>
                                    <p class="text-slate-200 text-xs">Role para baixo e toque em <span class="text-amber-500 font-bold">Adicionar à Tela de Início</span>.</p>
                                </div>
                            </div>

                            <button @click="showIosGuide = false; localStorage.setItem('pwa_ios_guide_dismissed', 'true')" 
                                    class="w-full py-4 bg-white/10 hover:bg-white/20 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl transition-all">
                                Entendido
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Update Toast -->
                <div x-show="showUpdateToast" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="-translate-y-full opacity-0"
                     x-transition:enter-end="translate-y-0 opacity-100"
                     class="absolute top-4 left-4 right-4 pointer-events-auto">
                    <div class="bg-amber-500 border border-white/20 rounded-2xl p-4 shadow-2xl flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-slate-900 font-black text-xs uppercase tracking-tight">Nova Versão!</h4>
                                <p class="text-slate-900/70 text-[10px] font-bold uppercase tracking-widest">Atualize para as melhorias</p>
                            </div>
                        </div>
                        <button @click="
                            if (registration && registration.waiting) {
                                registration.waiting.postMessage('SKIP_WAITING');
                            }
                            window.location.reload();
                        " class="px-6 py-2 bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest rounded-xl">Atualizar</button>
                    </div>
                </div>
            </div>
        </div>
    </body>

    <script>
    // Compartilhar ou baixar anexo (PWA mobile — evita abrir doc preso no app)
    async function shareOrDownloadFile(url, fileName, title, text) {
        try {
            const response = await fetch(url, { credentials: 'include' });
            if (!response.ok) throw new Error('Falha ao carregar arquivo');

            const blob = await response.blob();
            const mime = blob.type || 'application/octet-stream';
            const name = fileName || 'anexo';
            const isMobile = window.matchMedia('(max-width: 640px)').matches
                || window.navigator.standalone
                || window.matchMedia('(display-mode: standalone)').matches;

            if (isMobile && navigator.share) {
                const file = new File([blob], name, { type: mime });
                if (navigator.canShare && navigator.canShare({ files: [file] })) {
                    await navigator.share({
                        files: [file],
                        title: title || name,
                        text: text || '',
                    });
                    return;
                }
            }

            const blobUrl = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = blobUrl;
            a.download = name;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(blobUrl);
            document.body.removeChild(a);
        } catch (error) {
            if (error.name !== 'AbortError') {
                alert('Erro ao abrir anexo. Tente novamente.');
            }
        }
    }

    // ============================================
    // OFFLINE SYNC — iOS + Android Compatible
    // ============================================

    function openOfflineDB() {
        return new Promise((resolve, reject) => {
            const req = indexedDB.open('diario-obras-offline', 1);
            req.onupgradeneeded = (e) => {
                const db = e.target.result;
                if (!db.objectStoreNames.contains('pending-posts')) {
                    db.createObjectStore('pending-posts', { keyPath: 'id', autoIncrement: true });
                }
            };
            req.onsuccess = () => resolve(req.result);
            req.onerror = () => reject(req.error);
        });
    }

    function fileToBase64(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = (e) => resolve(e.target.result.split(',')[1]);
            reader.onerror = reject;
            reader.readAsDataURL(file);
        });
    }

    function base64ToBlob(base64, mime) {
        const binary = atob(base64);
        const bytes = new Uint8Array(binary.length);
        for (let i = 0; i < binary.length; i++) bytes[i] = binary.charCodeAt(i);
        return new Blob([bytes], { type: mime });
    }

    function showToast(msg, color = 'green') {
        const toast = document.createElement('div');
        toast.innerHTML = msg;
        const palette = {
            green: 'bg-green-500/20 border-green-500/50 text-green-400',
            rose: 'bg-rose-500/20 border-rose-500/50 text-rose-400',
            amber: 'bg-amber-500/20 border-amber-500/50 text-amber-400',
        };
        toast.className = `fixed top-20 right-4 z-[200] p-4 ${palette[color] || palette.green} rounded-2xl text-sm backdrop-blur-xl shadow-2xl font-bold transition-all`;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 5000);
    }

    // ---- CLIENT-SIDE SYNC (runs in the page, works on iOS) ----
    async function clientSyncPendingPosts() {
        if (!navigator.onLine) return;

        const db = await openOfflineDB();
        const posts = await new Promise((res, rej) => {
            const tx = db.transaction('pending-posts', 'readonly');
            const req = tx.objectStore('pending-posts').getAll();
            req.onsuccess = () => res(req.result);
            req.onerror = () => rej(req.error);
        });

        if (posts.length === 0) return;

        // Fetch a fresh CSRF token
        let freshToken = null;
        try {
            const r = await fetch("{{ route('csrf.token') }}", { credentials: 'include' });
            if (r.ok) freshToken = (await r.json()).token;
        } catch(e) {}

        let synced = 0;
        let removed = 0;
        for (const post of posts) {
            try {
                if (!post.fotoBase64) {
                    const delTx = db.transaction('pending-posts', 'readwrite');
                    delTx.objectStore('pending-posts').delete(post.id);
                    removed++;
                    continue;
                }

                const fd = new FormData();
                fd.append('texto', post.texto || '');
                fd.append('_token', freshToken || post.token);
                fd.append('foto', base64ToBlob(post.fotoBase64, post.fotoMime), post.fotoName);

                const resp = await fetch("{{ route('diario-posts.store') }}", {
                    method: 'POST',
                    body: fd,
                    credentials: 'include',
                    redirect: 'follow',
                });

                if (resp.ok || resp.redirected || resp.status === 302 || resp.status === 200) {
                    const delTx = db.transaction('pending-posts', 'readwrite');
                    delTx.objectStore('pending-posts').delete(post.id);
                    synced++;
                } else if (resp.status === 422) {
                    const delTx = db.transaction('pending-posts', 'readwrite');
                    delTx.objectStore('pending-posts').delete(post.id);
                    removed++;
                }
            } catch(e) {
                console.error('Sync error for post', post.id, e);
            }
        }

        if (synced > 0) {
            showToast(`✓ ${synced} publicação(ões) enviada(s) com sucesso!`);
        }
        if (removed > 0) {
            showToast('Publicação inválida removida da fila: foto obrigatória.', 'rose');
        }
        if (synced > 0 || removed > 0) {
            updatePendingBadge();
        }
    }

    // Update the pending posts badge in the UI
    async function updatePendingBadge() {
        try {
            const db = await openOfflineDB();
            const count = await new Promise((res) => {
                const req = db.transaction('pending-posts', 'readonly').objectStore('pending-posts').count();
                req.onsuccess = () => res(req.result);
            });
            const badge = document.getElementById('pending-posts-badge');
            if (badge) {
                badge.textContent = count;
                badge.style.display = count > 0 ? 'flex' : 'none';
            }
        } catch(e) {}
    }

    // ---- FORM SUBMISSION ----
    async function submitPost(event, alpineData) {
        const texto = document.getElementById('post-texto').value;
        const token = document.querySelector('#diario-post-form [name="_token"]').value;
        const fileInput = document.getElementById('post-foto');
        const file = fileInput && fileInput.files[0];

        if (!file) {
            showToast('Selecione uma foto para publicar no diário.', 'rose');
            return;
        }

        alpineData.submitting = true;

        if (navigator.onLine) {
            document.getElementById('diario-post-form').submit();
        } else {
            try {
                const fotoBase64 = await fileToBase64(file);
                const fotoMime = file.type;
                const fotoName = file.name;

                const db = await openOfflineDB();
                await new Promise((res, rej) => {
                    const tx = db.transaction('pending-posts', 'readwrite');
                    const req = tx.objectStore('pending-posts').add({
                        texto, token, fotoBase64, fotoMime, fotoName,
                        createdAt: new Date().toISOString()
                    });
                    req.onsuccess = res;
                    req.onerror = rej;
                });

                // Android: register Background Sync
                if ('serviceWorker' in navigator) {
                    const reg = await navigator.serviceWorker.ready;
                    if ('sync' in reg) {
                        await reg.sync.register('sync-diario-posts');
                    }
                }

                alpineData.submitting = false;
                alpineData.queued = true;
                updatePendingBadge();
            } catch (err) {
                console.error('Erro ao salvar offline:', err);
                alpineData.submitting = false;
                alert('Erro ao salvar localmente. Tente novamente.');
            }
        }
    }

    // ---- EVENT LISTENERS ----

    // iOS + Android: sync when connection returns while app is open
    window.addEventListener('online', async () => {
        document.getElementById('offline-bar')?.remove();
        await clientSyncPendingPosts();
    });

    // iOS + Android: sync on page load (catches posts queued while browser was closed)
    document.addEventListener('DOMContentLoaded', () => {
        clientSyncPendingPosts();
        updatePendingBadge();

        // Show offline bar if already offline
        if (!navigator.onLine) {
            const bar = document.createElement('div');
            bar.id = 'offline-bar';
            bar.innerHTML = '📡 Sem conexão — publicações serão enviadas automaticamente quando a internet voltar';
            bar.className = 'fixed bottom-20 left-4 right-4 z-[200] p-3 bg-amber-600 text-white text-xs font-bold text-center rounded-2xl shadow-2xl';
            document.body.appendChild(bar);
        }
    });

    window.addEventListener('offline', () => {
        if (document.getElementById('offline-bar')) return;
        const bar = document.createElement('div');
        bar.id = 'offline-bar';
        bar.innerHTML = '📡 Sem conexão — publicações serão enviadas automaticamente quando a internet voltar';
        bar.className = 'fixed bottom-20 left-4 right-4 z-[200] p-3 bg-amber-600 text-white text-xs font-bold text-center rounded-2xl shadow-2xl';
        document.body.appendChild(bar);
    });

    // Android: listen for sync-success message from Service Worker
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.addEventListener('message', (event) => {
            if (event.data?.type === 'sync-success') {
                showToast('✓ Post sincronizado com sucesso!');
                updatePendingBadge();
            }
            if (event.data?.type === 'sync-error') {
                showToast(event.data.message || 'Erro ao sincronizar publicação.', 'rose');
                updatePendingBadge();
            }
        });
    }
    </script>
</html>
