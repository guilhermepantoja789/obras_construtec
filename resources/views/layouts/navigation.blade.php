<nav x-data="{ open: false }" class="bg-slate-900/80 backdrop-blur-lg border-b border-white/10 sticky top-0 z-50 safe-area-top">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-amber-500 rounded-lg flex items-center justify-center shadow-lg shadow-amber-500/20 border border-amber-400/50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-900" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <span class="font-bold text-white text-lg tracking-tight hidden lg:block">Diário</span>
                    </a>
                </div>

                <!-- Obra Selector -->
                <div class="ml-4 flex items-center" x-data="{ search: '' }">
                    <x-dropdown align="left" width="64">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-white/10 text-xs font-bold uppercase tracking-widest rounded-lg text-amber-500 bg-white/5 hover:bg-white/10 transition backdrop-blur-sm max-w-[150px] sm:max-w-[250px]">
                                <span class="truncate">{{ $activeObra->nome ?? 'Selecionar Obra' }}</span>
                                <svg class="ml-2 h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <div class="p-2 border-b border-white/5" @click.stop>
                                <div class="relative">
                                    <input type="text" 
                                           x-model="search" 
                                           placeholder="Buscar obra..." 
                                           class="w-full bg-slate-900 border-white/10 rounded-lg text-xs text-white placeholder-slate-600 focus:border-amber-500 focus:ring-0 py-1.5 pl-8">
                                    <div class="absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-600">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="max-h-64 overflow-y-auto custom-scrollbar" @click.stop>
                                @foreach($availableObras as $obra)
                                    <form action="{{ route('context.switch') }}" method="POST" x-show="'{{ strtolower($obra->nome) }}'.includes(search.toLowerCase())">
                                        @csrf
                                        <input type="hidden" name="obra_id" value="{{ $obra->id }}">
                                        <button type="submit" class="w-full text-left px-4 py-2.5 text-xs text-slate-300 hover:bg-amber-500/10 hover:text-amber-500 transition-colors {{ (isset($activeObra) && $activeObra->id == $obra->id) ? 'bg-white/5 text-amber-500 font-bold border-l-2 border-amber-500' : '' }}">
                                            <div class="flex items-center gap-2">
                                                <div class="w-1.5 h-1.5 rounded-full {{ $obra->status == 'concluida' ? 'bg-green-500' : 'bg-blue-500' }}"></div>
                                                <span class="truncate">{{ $obra->nome }}</span>
                                            </div>
                                        </button>
                                    </form>
                                @endforeach
                                
                                <div x-show="search !== '' && !Array.from($el.children).some(el => el.style.display !== 'none')" class="px-4 py-4 text-center text-[10px] text-slate-600 uppercase tracking-widest">
                                    Nenhuma obra encontrada
                                </div>
                            </div>
                        </x-slot>
                    </x-dropdown>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-4 lg:space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-slate-400 hover:text-white border-transparent hover:border-amber-500 transition-all">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    <x-nav-link :href="route('obras.index')" :active="request()->routeIs('obras.*')" class="text-slate-400 hover:text-white border-transparent hover:border-amber-500 transition-all">
                        {{ __('Obras') }}
                    </x-nav-link>

                    <x-nav-link :href="route('etapa-obras.index')" :active="request()->routeIs('etapa-obras.*')" class="text-slate-400 hover:text-white border-transparent hover:border-amber-500 transition-all">
                        {{ __('Cronograma') }}
                    </x-nav-link>

                    @if(Auth::user()->isChefe())
                        <x-nav-link :href="route('propostas.index')" :active="request()->routeIs('propostas.*')" class="text-slate-400 hover:text-white border-transparent hover:border-amber-500 transition-all">
                            {{ __('Propostas') }}
                        </x-nav-link>

                        <x-nav-link :href="route('financeiro.index')" :active="request()->routeIs('financeiro.*')" class="text-slate-400 hover:text-white border-transparent hover:border-amber-500 transition-all">
                            {{ __('Financeiro') }}
                        </x-nav-link>
                    @endif

                    @if(!Auth::user()->isClient())
                        <x-nav-link :href="route('nota-fiscals.index')" :active="request()->routeIs('nota-fiscals.*')" class="text-slate-400 hover:text-white border-transparent hover:border-amber-500 transition-all">
                            {{ __('Notas') }}
                        </x-nav-link>
                    @endif

                    @if(Auth::user()->isChefe())
                        <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')" class="text-slate-400 hover:text-white border-transparent hover:border-amber-500 transition-all">
                            {{ __('Equipe') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Geral Menu Trigger (Desktop) -->
            <div class="hidden sm:flex items-center ms-4">
                <button @click="showGeneralMenu = !showGeneralMenu" 
                        class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-white/5 transition-colors border border-transparent hover:border-white/10 flex items-center gap-2 group"
                        :class="showGeneralMenu ? 'text-amber-500 bg-white/5 border-white/10' : ''">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                    </svg>
                    <span class="text-[10px] font-bold uppercase tracking-widest hidden lg:block">Menu</span>
                </button>
            </div>

            <!-- Settings Dropdown -->
            <div class="flex items-center ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center transition ease-in-out duration-150">
                            <div class="w-8 h-8 rounded-full bg-amber-500 flex items-center justify-center text-slate-900 font-black shadow-lg shadow-amber-500/20 border border-white/10">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-2 border-b border-white/5">
                            <p class="text-xs text-slate-500 font-bold uppercase tracking-widest">Conta</p>
                            <p class="text-sm font-bold text-white truncate">{{ Auth::user()->name }}</p>
                        </div>

                        <x-dropdown-link :href="route('profile.edit')" class="dark:hover:bg-amber-500/10 dark:hover:text-amber-500">
                            {{ __('Configurações') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();"
                                    class="dark:hover:bg-red-500/10 dark:hover:text-red-500">
                                {{ __('Sair') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger (Hidden for Bottom Nav) -->
            <div class="-me-2 flex items-center sm:hidden hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-lg text-slate-400 hover:text-white hover:bg-white/5 focus:outline-none transition duration-150 ease-in-out border border-transparent hover:border-white/10">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu (Disabled for Bottom Nav) -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-slate-900/95 backdrop-blur-xl border-t border-white/5">
        <!-- Content removed to favor bottom navigation -->
    </div>
</nav>
