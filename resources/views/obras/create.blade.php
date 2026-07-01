<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('obras.index') }}" class="sm:hidden flex-shrink-0 p-2 -ml-2 rounded-xl text-slate-400 active:bg-white/10 active:text-white transition-colors" aria-label="Voltar">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <p class="text-[10px] font-black text-amber-500 uppercase tracking-[0.2em] sm:hidden">Cadastro</p>
                <h2 class="font-semibold text-lg sm:text-xl text-white leading-tight">
                    {{ __('Nova Obra') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto -mx-4 sm:mx-auto" x-data="obraForm()">
        <div class="bg-white/5 sm:backdrop-blur-xl border-y sm:border border-white/10 overflow-hidden shadow-2xl sm:rounded-2xl">
            <form id="obra-form" action="{{ route('obras.store') }}" method="POST"
                  class="pb-28 sm:pb-0"
                  @submit="handleSubmit($event)">
                @csrf

                <div class="p-4 sm:p-8 space-y-5 sm:space-y-8">

                    {{-- Seção: Identificação --}}
                    <section class="space-y-4">
                        <div class="flex items-center gap-3 pb-1">
                            <div class="w-8 h-8 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Identificação</h3>
                                <p class="text-xs text-slate-600 hidden sm:block">Nome e status inicial da obra</p>
                            </div>
                        </div>

                        <div class="space-y-4 p-4 sm:p-5 bg-white/[0.03] rounded-2xl border border-white/5">
                            <div>
                                <x-input-label for="nome" :value="__('Nome da Obra')" />
                                <x-text-input id="nome" name="nome" type="text" class="mt-1 block w-full mobile-input" :value="old('nome')" required autofocus placeholder="Ex: Edifício Horizonte" />
                                <x-input-error class="mt-2" :messages="$errors->get('nome')" />
                            </div>

                            <div>
                                <x-input-label for="status" :value="__('Status Inicial')" />
                                <select id="status" name="status" class="mobile-input mt-1 block w-full bg-white/5 border-white/10 focus:border-amber-500 focus:ring-amber-500 rounded-xl shadow-sm text-white backdrop-blur-sm">
                                    <option value="planejamento" class="bg-slate-900">Planejamento</option>
                                    <option value="em_andamento" class="bg-slate-900" selected>Em Andamento</option>
                                    <option value="paralisada" class="bg-slate-900">Paralisada</option>
                                    <option value="concluida" class="bg-slate-900">Concluída</option>
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('status')" />
                            </div>
                        </div>
                    </section>

                    {{-- Seção: Endereço --}}
                    <section class="space-y-4">
                        <div class="flex items-center gap-3 pb-1">
                            <div class="w-8 h-8 rounded-xl bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Endereço</h3>
                                <p class="text-xs text-slate-600 hidden sm:block">Localização e ponto de referência</p>
                            </div>
                        </div>

                        <div class="space-y-4 p-4 sm:p-5 bg-white/[0.03] rounded-2xl border border-white/5">
                            <div class="flex gap-2">
                                <div class="flex-1 min-w-0">
                                    <x-input-label for="cep" :value="__('CEP')" />
                                    <x-text-input id="cep" name="cep" type="text" inputmode="numeric" class="mt-1 block w-full mobile-input" :value="old('cep')" placeholder="00000-000" maxlength="9" />
                                    <x-input-error class="mt-2" :messages="$errors->get('cep')" />
                                </div>
                                <div class="flex items-end flex-shrink-0">
                                    <button type="button" @click="buscarCep()" :disabled="cepLoading"
                                        class="h-[48px] px-4 bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 rounded-xl text-[10px] font-black uppercase tracking-widest active:scale-95 transition-all disabled:opacity-50 whitespace-nowrap">
                                        <span x-show="!cepLoading">Buscar</span>
                                        <span x-show="cepLoading">...</span>
                                    </button>
                                </div>
                            </div>

                            <div>
                                <x-input-label for="logradouro" :value="__('Logradouro')" />
                                <x-text-input id="logradouro" name="logradouro" type="text" class="mt-1 block w-full mobile-input" :value="old('logradouro')" placeholder="Rua, Avenida..." />
                                <x-input-error class="mt-2" :messages="$errors->get('logradouro')" />
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 sm:gap-4">
                                <div class="col-span-2 sm:col-span-1">
                                    <x-input-label for="bairro" :value="__('Bairro')" />
                                    <x-text-input id="bairro" name="bairro" type="text" class="mt-1 block w-full mobile-input" :value="old('bairro')" />
                                    <x-input-error class="mt-2" :messages="$errors->get('bairro')" />
                                </div>
                                <div>
                                    <x-input-label for="cidade" :value="__('Cidade')" />
                                    <x-text-input id="cidade" name="cidade" type="text" class="mt-1 block w-full mobile-input" :value="old('cidade')" />
                                    <x-input-error class="mt-2" :messages="$errors->get('cidade')" />
                                </div>
                                <div>
                                    <x-input-label for="estado" :value="__('UF')" />
                                    <x-text-input id="estado" name="estado" type="text" class="mt-1 block w-full mobile-input uppercase" :value="old('estado')" maxlength="2" placeholder="AM" />
                                    <x-input-error class="mt-2" :messages="$errors->get('estado')" />
                                </div>
                            </div>

                            <div>
                                <x-input-label for="localizacao" :value="__('Complemento / Referência')" />
                                <x-text-input id="localizacao" name="localizacao" type="text" class="mt-1 block w-full mobile-input" :value="old('localizacao')" placeholder="Ex: Próximo ao metrô" />
                                <x-input-error class="mt-2" :messages="$errors->get('localizacao')" />
                            </div>
                        </div>
                    </section>

                    {{-- Seção: Prazos --}}
                    <section class="space-y-4">
                        <div class="flex items-center gap-3 pb-1">
                            <div class="w-8 h-8 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Prazos</h3>
                                <p class="text-xs text-slate-600 hidden sm:block">Datas e duração estimada</p>
                            </div>
                        </div>

                        <div class="space-y-4 p-4 sm:p-5 bg-white/[0.03] rounded-2xl border border-white/5">
                            <div class="grid grid-cols-2 gap-3 sm:gap-4">
                                <x-date-br-input name="data_inicio" label="Início" :optional="true" inputClass="mobile-input" />
                                <x-date-br-input name="data_fim_prevista" label="Término" :optional="true" inputClass="mobile-input" />
                            </div>

                            <div>
                                <x-input-label for="prazo_dias" :value="__('Prazo Total (Dias)')" />
                                <x-text-input id="prazo_dias" name="prazo_dias" type="number" inputmode="numeric" min="0" class="mt-1 block w-full mobile-input" :value="old('prazo_dias')" placeholder="Ex: 180" />
                                <p x-show="prazoCalculado !== null && !prazoManual" x-cloak class="mt-1.5 text-[10px] text-blue-400 font-bold uppercase tracking-widest">
                                    Calculado automaticamente: <span x-text="prazoCalculado"></span> dias
                                </p>
                                <p class="mt-1.5 text-[10px] text-slate-600">Opcional — calculado pelas datas quando início e término estiverem preenchidos</p>
                                <x-input-error class="mt-2" :messages="$errors->get('prazo_dias')" />
                            </div>
                        </div>
                    </section>

                    {{-- Seção: Contratantes --}}
                    <section class="space-y-4">
                        <div class="flex items-center gap-3 pb-1">
                            <div class="w-8 h-8 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Contratantes</h3>
                                <p class="text-xs text-slate-600 hidden sm:block">Cliente, executora e responsável técnico</p>
                            </div>
                        </div>

                        <div class="space-y-4 p-4 sm:p-5 bg-white/[0.03] rounded-2xl border border-white/5">
                            <div>
                                <x-input-label for="contratante" :value="__('Contratante (Cliente)')" />
                                <x-text-input id="contratante" name="contratante" type="text" class="mt-1 block w-full mobile-input" :value="old('contratante')" placeholder="Ex: Nome da Empresa ou Cliente" />
                                <x-input-error class="mt-2" :messages="$errors->get('contratante')" />
                            </div>
                            <div>
                                <x-input-label for="cnpj_contratante" :value="__('CNPJ do Contratante')" />
                                <x-text-input id="cnpj_contratante" name="cnpj_contratante" type="text" inputmode="numeric" class="mt-1 block w-full mobile-input cnpj-mask" :value="old('cnpj_contratante')" placeholder="00.000.000/0000-00" />
                                <x-input-error class="mt-2" :messages="$errors->get('cnpj_contratante')" />
                            </div>

                            <div class="border-t border-white/5 pt-4">
                                <div>
                                    <x-input-label for="empresa_contratada" :value="__('Empresa Executora')" />
                                    <x-text-input id="empresa_contratada" name="empresa_contratada" type="text" class="mt-1 block w-full mobile-input" :value="old('empresa_contratada')" placeholder="Ex: Nome da sua Construtora" />
                                    <x-input-error class="mt-2" :messages="$errors->get('empresa_contratada')" />
                                </div>
                            </div>
                            <div>
                                <x-input-label for="cnpj_empresa_contratada" :value="__('CNPJ da Executora')" />
                                <x-text-input id="cnpj_empresa_contratada" name="cnpj_empresa_contratada" type="text" inputmode="numeric" class="mt-1 block w-full mobile-input cnpj-mask" :value="old('cnpj_empresa_contratada')" placeholder="00.000.000/0000-00" />
                                <x-input-error class="mt-2" :messages="$errors->get('cnpj_empresa_contratada')" />
                            </div>

                            <div class="border-t border-white/5 pt-4">
                                <x-input-label for="engenheiro_responsavel" :value="__('Engenheiro Responsável (CREA)')" />
                                <x-text-input id="engenheiro_responsavel" name="engenheiro_responsavel" type="text" class="mt-1 block w-full mobile-input" :value="old('engenheiro_responsavel')" placeholder="Ex: Ronaldo Souza 24958-AM" />
                                <x-input-error class="mt-2" :messages="$errors->get('engenheiro_responsavel')" />
                            </div>
                        </div>
                    </section>
                </div>

                {{-- Rodapé desktop --}}
                <div class="hidden sm:flex items-center justify-end gap-4 border-t border-white/5 px-8 py-6">
                    <a href="{{ route('obras.index') }}" class="text-sm text-slate-400 hover:text-white transition-colors">
                        {{ __('Cancelar') }}
                    </a>
                    <x-primary-button id="btn-submit-desktop" x-bind:disabled="submitting">
                        <span x-show="!submitting">{{ __('Criar Obra') }}</span>
                        <span x-show="submitting">Salvando...</span>
                    </x-primary-button>
                </div>
            </form>
        </div>

        {{-- Barra fixa mobile --}}
        <div class="sm:hidden fixed bottom-[4.25rem] left-0 right-0 z-30 px-4 py-3 bg-slate-900/95 backdrop-blur-xl border-t border-white/10 safe-area-bottom">
            <div class="flex gap-3">
                <a href="{{ route('obras.index') }}" class="flex-shrink-0 h-12 px-4 flex items-center justify-center rounded-xl bg-white/5 border border-white/10 text-slate-400 text-[10px] font-black uppercase tracking-widest active:bg-white/10 transition-colors">
                    Cancelar
                </a>
                <button type="submit" form="obra-form" :disabled="submitting"
                    class="flex-1 h-12 bg-amber-500 active:bg-amber-400 text-slate-900 font-black rounded-xl transition-all shadow-lg shadow-amber-500/20 uppercase tracking-widest text-xs disabled:opacity-60">
                    <span x-show="!submitting">Criar Obra</span>
                    <span x-show="submitting">Salvando...</span>
                </button>
            </div>
        </div>
    </div>

    <style>
        .mobile-input {
            font-size: 16px;
            min-height: 48px;
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
        }
        @media (min-width: 640px) {
            .mobile-input {
                font-size: 0.875rem;
                min-height: auto;
                padding-top: 0.5rem;
                padding-bottom: 0.5rem;
            }
        }
    </style>

    @include('partials.date-br-script')

    <script>
        function obraForm() {
            return {
                submitting: false,
                cepLoading: false,
                prazoManual: {{ old('prazo_dias') ? 'true' : 'false' }},
                prazoCalculado: null,

                init() {
                    initDateBrFields(this.$root);
                    this.applyCepMask();
                    this.applyCnpjMasks();
                    ['data_inicio', 'data_fim_prevista'].forEach((id) => {
                        const el = document.getElementById(id);
                        el?.addEventListener('input', () => this.calcularPrazo());
                        el?.addEventListener('change', () => this.calcularPrazo());
                    });
                    document.getElementById('prazo_dias')?.addEventListener('input', (e) => {
                        if (e.target.value !== '') this.prazoManual = true;
                    });
                    this.calcularPrazo();
                },

                handleSubmit(event) {
                    const result = prepareDateBrFieldsForSubmit(event.target);
                    if (!result.ok) {
                        event.preventDefault();
                        result.input.focus();
                        alert('Informe uma data válida no formato DD/MM/AAAA.');
                        return;
                    }

                    this.submitting = true;
                },

                applyCepMask() {
                    const cep = document.getElementById('cep');
                    cep.addEventListener('input', function (e) {
                        let x = e.target.value.replace(/\D/g, '').match(/(\d{0,5})(\d{0,3})/);
                        e.target.value = !x[2] ? x[1] : x[1] + '-' + x[2];
                    });
                },

                applyCnpjMasks() {
                    document.querySelectorAll('.cnpj-mask').forEach(input => {
                        input.addEventListener('input', function (e) {
                            let x = e.target.value.replace(/\D/g, '').match(/(\d{0,2})(\d{0,3})(\d{0,3})(\d{0,4})(\d{0,2})/);
                            e.target.value = !x[2] ? x[1] : x[1] + '.' + x[2] + (x[3] ? '.' + x[3] : '') + (x[4] ? '/' + x[4] : '') + (x[5] ? '-' + x[5] : '');
                        });
                    });
                },

                calcularPrazo() {
                    if (this.prazoManual) return;

                    const inicioEl = document.getElementById('data_inicio');
                    const fimEl = document.getElementById('data_fim_prevista');
                    const prazoEl = document.getElementById('prazo_dias');
                    const inicio = readDateFieldValue(inicioEl);
                    const fim = readDateFieldValue(fimEl);

                    if (inicio === null || fim === null) {
                        return;
                    }

                    if (!inicio || !fim) {
                        this.prazoCalculado = null;
                        prazoEl.value = '';
                        return;
                    }

                    const d1 = new Date(inicio + 'T00:00:00');
                    const d2 = new Date(fim + 'T00:00:00');
                    const diff = Math.round((d2 - d1) / (1000 * 60 * 60 * 24));

                    if (diff >= 0) {
                        this.prazoCalculado = diff;
                        prazoEl.value = diff;
                    } else {
                        this.prazoCalculado = null;
                        prazoEl.value = '';
                    }
                },

                buscarCep() {
                    const cep = document.getElementById('cep').value.replace(/\D/g, '');
                    if (cep.length !== 8) {
                        alert('Informe um CEP válido com 8 dígitos.');
                        return;
                    }

                    this.cepLoading = true;

                    fetch(`https://viacep.com.br/ws/${cep}/json/`)
                        .then(response => response.json())
                        .then(data => {
                            if (!data.erro) {
                                document.getElementById('logradouro').value = data.logradouro;
                                document.getElementById('bairro').value = data.bairro;
                                document.getElementById('cidade').value = data.localidade;
                                document.getElementById('estado').value = data.uf;
                                document.getElementById('logradouro').focus();
                            } else {
                                alert('CEP não encontrado.');
                            }
                        })
                        .catch(error => console.error('Erro ao buscar CEP:', error))
                        .finally(() => {
                            this.cepLoading = false;
                        });
                }
            };
        }
    </script>
</x-app-layout>
