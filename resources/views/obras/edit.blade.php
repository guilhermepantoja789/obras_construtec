<x-app-layout>
    @php
        $prazoExibicao = old('prazo_dias');
        if ($prazoExibicao === null) {
            $prazoExibicao = $obra->prazo_dias > 0 ? $obra->prazo_dias : '';
        }

        $prazoManualInicial = old('prazo_dias') !== null && old('prazo_dias') !== '';
        if (! $prazoManualInicial && $obra->prazo_dias > 0) {
            if ($obra->data_inicio && $obra->data_fim_prevista) {
                $prazoManualInicial = (int) $obra->prazo_dias !== (int) $obra->data_inicio->diffInDays($obra->data_fim_prevista);
            } else {
                $prazoManualInicial = true;
            }
        }
    @endphp

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Editar Obra: ') . $obra->nome }}
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto" x-data="obraEditForm()">
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 overflow-hidden shadow-2xl rounded-2xl">
            <div class="p-8">
                <form id="obra-edit-form" action="{{ route('obras.update', $obra) }}" method="POST" class="space-y-6" @submit="handleSubmit($event)">
                    @csrf
                    @method('PATCH')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <x-input-label for="nome" :value="__('Nome da Obra')" />
                            <x-text-input id="nome" name="nome" type="text" class="mt-1 block w-full" :value="old('nome', $obra->nome)" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('nome')" />
                        </div>

                        <!-- Seção de Endereço via CEP -->
                        <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-4 p-4 bg-white/5 rounded-xl border border-white/5">
                            <div class="md:col-span-1">
                                <x-input-label for="cep" :value="__('CEP')" />
                                <x-text-input id="cep" name="cep" type="text" class="mt-1 block w-full" :value="old('cep', $obra->cep)" placeholder="00000-000" maxlength="9" onblur="buscarCep(this.value)" />
                                <x-input-error class="mt-2" :messages="$errors->get('cep')" />
                            </div>
                            <div class="md:col-span-2">
                                <x-input-label for="logradouro" :value="__('Logradouro')" />
                                <x-text-input id="logradouro" name="logradouro" type="text" class="mt-1 block w-full" :value="old('logradouro', $obra->logradouro)" />
                                <x-input-error class="mt-2" :messages="$errors->get('logradouro')" />
                            </div>
                            <div>
                                <x-input-label for="bairro" :value="__('Bairro')" />
                                <x-text-input id="bairro" name="bairro" type="text" class="mt-1 block w-full" :value="old('bairro', $obra->bairro)" />
                                <x-input-error class="mt-2" :messages="$errors->get('bairro')" />
                            </div>
                            <div>
                                <x-input-label for="cidade" :value="__('Cidade')" />
                                <x-text-input id="cidade" name="cidade" type="text" class="mt-1 block w-full" :value="old('cidade', $obra->cidade)" />
                                <x-input-error class="mt-2" :messages="$errors->get('cidade')" />
                            </div>
                            <div>
                                <x-input-label for="estado" :value="__('Estado (UF)')" />
                                <x-text-input id="estado" name="estado" type="text" class="mt-1 block w-full" :value="old('estado', $obra->estado)" maxlength="2" />
                                <x-input-error class="mt-2" :messages="$errors->get('estado')" />
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="localizacao" :value="__('Complemento / Ponto de Referência')" />
                            <x-text-input id="localizacao" name="localizacao" type="text" class="mt-1 block w-full" :value="old('localizacao', $obra->localizacao)" />
                            <x-input-error class="mt-2" :messages="$errors->get('localizacao')" />
                        </div>

                        <x-date-br-input
                            name="data_inicio"
                            label="Data de Início"
                            :optional="true"
                            :value="old('data_inicio', $obra->data_inicio ? $obra->data_inicio->format('Y-m-d') : '')"
                        />

                        <x-date-br-input
                            name="data_fim_prevista"
                            label="Previsão de Término"
                            :optional="true"
                            :value="old('data_fim_prevista', $obra->data_fim_prevista ? $obra->data_fim_prevista->format('Y-m-d') : '')"
                        />

                        <div>
                            <x-input-label for="prazo_dias" :value="__('Prazo Total (Dias)')" />
                            <x-text-input id="prazo_dias" name="prazo_dias" type="number" inputmode="numeric" min="0" class="mt-1 block w-full" :value="$prazoExibicao" placeholder="Ex: 180" />
                            <p x-show="prazoCalculado !== null && !prazoManual" x-cloak class="mt-1.5 text-[10px] text-blue-400 font-bold uppercase tracking-widest">
                                Calculado automaticamente: <span x-text="prazoCalculado"></span> dias
                            </p>
                            <p class="mt-1.5 text-[10px] text-slate-600">Opcional — calculado pelas datas quando início e término estiverem preenchidos</p>
                            <x-input-error class="mt-2" :messages="$errors->get('prazo_dias')" />
                        </div>

                        <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-white/5 rounded-xl border border-white/5">
                            <div class="space-y-4">
                                <div>
                                    <x-input-label for="contratante" :value="__('Contratante (Cliente)')" />
                                    <x-text-input id="contratante" name="contratante" type="text" class="mt-1 block w-full" :value="old('contratante', $obra->contratante)" placeholder="Ex: Nome da Empresa ou Cliente" />
                                    <x-input-error class="mt-2" :messages="$errors->get('contratante')" />
                                </div>
                                <div>
                                    <x-input-label for="cnpj_contratante" :value="__('CNPJ do Contratante')" />
                                    <x-text-input id="cnpj_contratante" name="cnpj_contratante" type="text" class="mt-1 block w-full cnpj-mask" :value="old('cnpj_contratante', $obra->cnpj_contratante)" placeholder="00.000.000/0000-00" />
                                    <x-input-error class="mt-2" :messages="$errors->get('cnpj_contratante')" />
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <x-input-label for="empresa_contratada" :value="__('Empresa Executora')" />
                                    <x-text-input id="empresa_contratada" name="empresa_contratada" type="text" class="mt-1 block w-full" :value="old('empresa_contratada', $obra->empresa_contratada)" placeholder="Ex: Nome da sua Construtora" />
                                    <x-input-error class="mt-2" :messages="$errors->get('empresa_contratada')" />
                                </div>
                                <div>
                                    <x-input-label for="cnpj_empresa_contratada" :value="__('CNPJ da Executora')" />
                                    <x-text-input id="cnpj_empresa_contratada" name="cnpj_empresa_contratada" type="text" class="mt-1 block w-full cnpj-mask" :value="old('cnpj_empresa_contratada', $obra->cnpj_empresa_contratada)" placeholder="00.000.000/0000-00" />
                                    <x-input-error class="mt-2" :messages="$errors->get('cnpj_empresa_contratada')" />
                                </div>
                            </div>
                            <div class="md:col-span-2">
                                <x-input-label for="engenheiro_responsavel" :value="__('Engenheiro Responsável (CREA)')" />
                                <x-text-input id="engenheiro_responsavel" name="engenheiro_responsavel" type="text" class="mt-1 block w-full" :value="old('engenheiro_responsavel', $obra->engenheiro_responsavel)" placeholder="Ex: Ronaldo Souza 24958-AM" />
                                <x-input-error class="mt-2" :messages="$errors->get('engenheiro_responsavel')" />
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="status" :value="__('Status')" />
                            <select id="status" name="status" class="mt-1 block w-full bg-white/5 border-white/10 focus:border-amber-500 focus:ring-amber-500 rounded-xl shadow-sm text-white backdrop-blur-sm">
                                <option value="planejamento" class="bg-slate-900" {{ old('status', $obra->status) == 'planejamento' ? 'selected' : '' }}>Planejamento</option>
                                <option value="em_andamento" class="bg-slate-900" {{ old('status', $obra->status) == 'em_andamento' ? 'selected' : '' }}>Em Andamento</option>
                                <option value="paralisada" class="bg-slate-900" {{ old('status', $obra->status) == 'paralisada' ? 'selected' : '' }}>Paralisada</option>
                                <option value="concluida" class="bg-slate-900" {{ old('status', $obra->status) == 'concluida' ? 'selected' : '' }}>Concluída</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('status')" />
                        </div>
                    </div>

                    <div class="mt-8 flex items-center justify-end gap-4 border-t border-white/5 pt-6">
                        <a href="{{ route('obras.index') }}" class="text-sm text-slate-400 hover:text-white transition-colors">
                            {{ __('Cancelar') }}
                        </a>
                        <x-primary-button id="btn-submit">{{ __('Salvar Alterações') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('partials.date-br-script')

    <script>
        function obraEditForm() {
            return {
                prazoManual: {{ $prazoManualInicial ? 'true' : 'false' }},
                prazoCalculado: null,

                init() {
                    initDateBrFields(this.$root);
                    ['data_inicio', 'data_fim_prevista'].forEach((id) => {
                        const el = document.getElementById(id);
                        el?.addEventListener('input', () => this.calcularPrazo());
                        el?.addEventListener('change', () => this.calcularPrazo());
                    });
                    document.getElementById('prazo_dias')?.addEventListener('input', (e) => {
                        this.prazoManual = e.target.value !== '';
                    });
                    this.calcularPrazo();
                },

                handleSubmit(event) {
                    const result = prepareDateBrFieldsForSubmit(event.target);
                    if (!result.ok) {
                        event.preventDefault();
                        result.input.focus();
                        alert('Informe uma data válida no formato DD/MM/AAAA.');
                    }
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
            };
        }

        function buscarCep(valor) {
            const cep = valor.replace(/\D/g, '');
            if (cep.length !== 8) return;

            const btn = document.getElementById('btn-submit');
            const originalText = btn.innerHTML;
            btn.innerHTML = 'Buscando...';
            btn.disabled = true;

            fetch(`https://viacep.com.br/ws/${cep}/json/`)
                .then(response => response.json())
                .then(data => {
                    if (!data.erro) {
                        document.getElementById('logradouro').value = data.logradouro;
                        document.getElementById('bairro').value = data.bairro;
                        document.getElementById('cidade').value = data.localidade;
                        document.getElementById('estado').value = data.uf;
                    }
                })
                .catch(error => console.error('Erro ao buscar CEP:', error))
                .finally(() => {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                });
        }

        document.getElementById('cep').addEventListener('input', function (e) {
            let x = e.target.value.replace(/\D/g, '').match(/(\d{0,5})(\d{0,3})/);
            e.target.value = !x[2] ? x[1] : x[1] + '-' + x[2];
        });

        // Máscara para CNPJ
        document.querySelectorAll('.cnpj-mask').forEach(input => {
            input.addEventListener('input', function (e) {
                let x = e.target.value.replace(/\D/g, '').match(/(\d{0,2})(\d{0,3})(\d{0,3})(\d{0,4})(\d{0,2})/);
                e.target.value = !x[2] ? x[1] : x[1] + '.' + x[2] + (x[3] ? '.' + x[3] : '') + (x[4] ? '/' + x[4] : '') + (x[5] ? '-' + x[5] : '');
            });
        });
    </script>
</x-app-layout>
