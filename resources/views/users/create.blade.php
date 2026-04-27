<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Novo Usuário') }}
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 overflow-hidden shadow-2xl rounded-2xl">
            <div class="p-8">
                <form action="{{ route('users.store') }}" method="POST" class="space-y-8">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Dados Básicos -->
                        <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="name" :value="__('Nome Completo')" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus placeholder="Ex: João Silva" />
                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            </div>

                            <div>
                                <x-input-label for="email" :value="__('E-mail corporativo')" />
                                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" required placeholder="joao@empresa.com" />
                                <x-input-error class="mt-2" :messages="$errors->get('email')" />
                            </div>

                            <div>
                                <x-input-label for="password" :value="__('Senha de Acesso')" />
                                <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
                                <x-input-error class="mt-2" :messages="$errors->get('password')" />
                            </div>

                            <div>
                                <x-input-label for="role" :value="__('Perfil de Acesso')" />
                                <select id="role" name="role" class="mt-1 block w-full bg-white/5 border-white/10 focus:border-amber-500 focus:ring-amber-500 rounded-xl shadow-sm text-white backdrop-blur-sm">
                                    <option value="operador" class="bg-slate-900" {{ old('role') == 'operador' ? 'selected' : '' }}>Operador de Obra (Preenche Diário)</option>
                                    <option value="cliente" class="bg-slate-900" {{ old('role') == 'cliente' ? 'selected' : '' }}>Cliente (Visualiza Relatórios)</option>
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('role')" />
                            </div>
                        </div>

                        <!-- Vínculo com Obras -->
                        <div class="md:col-span-2 pt-6 border-t border-white/5">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                                <div>
                                    <h3 class="text-sm font-bold text-white uppercase tracking-widest mb-1">Acesso às Obras Ativas</h3>
                                    <p class="text-xs text-slate-500">Selecione as obras em andamento para este usuário.</p>
                                </div>
                                
                                <!-- Busca Local de Obras -->
                                <div class="relative w-full sm:w-64">
                                    <input type="text" id="search-obras" placeholder="Filtrar obras..." class="w-full bg-white/5 border-white/10 focus:border-amber-500 focus:ring-amber-500 rounded-lg text-xs text-white placeholder-slate-600 pl-9 py-2 transition-all">
                                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Container com Scroll -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar" id="obras-container">
                                @foreach($obras as $obra)
                                    <label class="obra-card relative flex flex-col p-4 bg-white/5 border border-white/10 rounded-2xl cursor-pointer hover:border-amber-500/50 transition-all group has-[:checked]:bg-amber-500/10 has-[:checked]:border-amber-500/50" data-nome="{{ strtolower($obra->nome) }}">
                                        <input type="checkbox" name="obras[]" value="{{ $obra->id }}" class="hidden peer">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="w-8 h-8 bg-slate-800 rounded-lg flex items-center justify-center text-amber-500 group-hover:scale-110 transition-transform">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                </svg>
                                            </div>
                                            <div class="w-5 h-5 border-2 border-white/10 rounded-full flex items-center justify-center peer-checked:bg-amber-500 peer-checked:border-amber-500 transition-colors">
                                                <svg class="w-3 h-3 text-slate-900 opacity-0 peer-checked:opacity-100" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <span class="text-sm font-bold text-white mb-1 obra-nome">{{ $obra->nome }}</span>
                                        <span class="text-[10px] text-slate-500 uppercase tracking-tighter truncate">{{ $obra->localizacao_exibicao }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('obras')" />
                        </div>
                    </div>

                    <script>
                        document.getElementById('search-obras')?.addEventListener('input', function(e) {
                            const term = e.target.value.toLowerCase();
                            document.querySelectorAll('.obra-card').forEach(card => {
                                const nome = card.getAttribute('data-nome');
                                if (nome.includes(term)) {
                                    card.classList.remove('hidden');
                                } else {
                                    card.classList.add('hidden');
                                }
                            });
                        });
                    </script>

                    <div class="mt-10 flex items-center justify-end gap-4 border-t border-white/5 pt-8">
                        <a href="{{ route('users.index') }}" class="text-sm text-slate-400 hover:text-white transition-colors">
                            {{ __('Cancelar') }}
                        </a>
                        <x-primary-button>{{ __('Cadastrar Usuário') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
