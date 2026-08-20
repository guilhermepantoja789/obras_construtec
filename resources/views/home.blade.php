<x-site-layout :hero="$hero">
    @php
        $whatsappDigits = preg_replace('/\D+/', '', (string) ($contact['whatsapp'] ?? ''));
        $phone = $contact['phone'] ?? '';
        $email = $contact['email'] ?? '';
        $instagram = ltrim((string) ($contact['instagram'] ?? ''), '@');
        $city = $contact['city'] ?? '';
        $hasContact = filled($phone) || filled($whatsappDigits) || filled($instagram) || filled($email);
        $ctaHref = auth()->check() ? route('dashboard') : route('login');
        $ctaLabel = auth()->check() ? 'Abrir sistema' : 'Área do cliente';
    @endphp

    <div class="min-h-screen">
        <header class="fixed inset-x-0 top-0 z-50 border-b border-white/10 bg-[#070b14]/75 backdrop-blur-xl">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-500 text-slate-900 shadow-lg shadow-amber-500/20">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </span>
                    <span>
                        <span class="block text-sm font-black uppercase tracking-[0.22em] text-white">Construtec</span>
                        <span class="block text-[10px] font-semibold uppercase tracking-[0.28em] text-amber-500">Postos de combustível</span>
                    </span>
                </a>

                <nav class="hidden items-center gap-8 text-[11px] font-bold uppercase tracking-[0.18em] text-slate-400 md:flex">
                    <a href="#sobre" class="transition hover:text-white">Sobre</a>
                    <a href="#servicos" class="transition hover:text-white">Serviços</a>
                    <a href="#obras" class="transition hover:text-white">Obras</a>
                    <a href="#contato" class="transition hover:text-white">Contato</a>
                </nav>

                <a href="{{ $ctaHref }}" class="rounded-full bg-amber-500 px-4 py-2 text-[11px] font-black uppercase tracking-[0.16em] text-slate-900 shadow-lg shadow-amber-500/20 transition hover:bg-amber-400">
                    {{ $ctaLabel }}
                </a>
            </div>
        </header>

        <section class="relative isolate min-h-[92vh] overflow-hidden">
            @if($hero)
                <img src="{{ asset($hero) }}" alt="Obra de posto de combustível da Construtec" class="absolute inset-0 h-full w-full object-cover">
            @endif
            <div class="absolute inset-0 bg-gradient-to-r from-[#070b14] via-[#070b14]/78 to-[#070b14]/25"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-[#070b14] via-transparent to-[#070b14]/40"></div>

            <div class="relative mx-auto flex min-h-[92vh] max-w-7xl flex-col justify-end px-4 pb-20 pt-32 sm:px-6 lg:px-8">
                <p class="mb-4 text-[11px] font-black uppercase tracking-[0.35em] text-amber-500">Manaus · Amazonas</p>
                <h1 class="max-w-3xl text-4xl font-black leading-[0.95] tracking-tight text-white sm:text-6xl lg:text-7xl">
                    Construção de postos de combustível.
                </h1>
                <p class="mt-6 max-w-xl text-base leading-relaxed text-slate-300 sm:text-lg">
                    Projeto, fundação, estrutura, cobertura, pista e infraestrutura — da obra civil à entrega do posto.
                </p>
                <div class="mt-10 flex flex-wrap items-center gap-4">
                    <a href="#obras" class="rounded-full bg-white px-6 py-3 text-[11px] font-black uppercase tracking-[0.18em] text-slate-900 transition hover:bg-slate-200">
                        Ver obras
                    </a>
                    <a href="{{ $ctaHref }}" class="rounded-full border border-white/20 px-6 py-3 text-[11px] font-black uppercase tracking-[0.18em] text-white transition hover:border-amber-500 hover:text-amber-400">
                        {{ $ctaLabel }}
                    </a>
                </div>
            </div>
        </section>

        <section class="border-y border-white/10 bg-white/[0.03]">
            <div class="mx-auto grid max-w-7xl grid-cols-2 gap-px divide-white/10 sm:grid-cols-4">
                <div class="px-6 py-10">
                    <p class="text-xl font-black text-white sm:text-2xl">Especialidade</p>
                    <p class="mt-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500">Postos de combustível</p>
                </div>
                <div class="px-6 py-10">
                    <p class="text-xl font-black text-white sm:text-2xl">Atuação</p>
                    <p class="mt-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500">Manaus, Amazonas</p>
                </div>
                <div class="px-6 py-10">
                    <p class="text-xl font-black text-white sm:text-2xl">Escopo</p>
                    <p class="mt-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500">Civil, metálica e pista</p>
                </div>
                <div class="px-6 py-10">
                    <p class="text-xl font-black text-white sm:text-2xl">Entrega</p>
                    <p class="mt-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500">Do projeto à operação</p>
                </div>
            </div>
        </section>

        <section id="servicos" class="mx-auto max-w-7xl px-4 py-24 sm:px-6 lg:px-8">
            <div class="max-w-2xl">
                <p class="text-[11px] font-black uppercase tracking-[0.32em] text-amber-500">Serviços</p>
                <h2 class="mt-3 text-3xl font-black tracking-tight text-white sm:text-4xl">Execução completa do posto, da fundação à pista.</h2>
                <p class="mt-4 text-slate-400">Especialização em postos de combustível, com engenharia e execução das disciplinas do complexo.</p>
            </div>

            <div class="mt-14 grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                @foreach ([
                    ['title' => 'Construção de postos', 'text' => 'Execução do empreendimento: planejamento, fundação, estrutura e acompanhamento técnico até a entrega.'],
                    ['title' => 'Estruturas e coberturas', 'text' => 'Marquise, pilares e tesouras metálicas com precisão dimensional e montagem em campo.'],
                    ['title' => 'Pista e infraestrutura', 'text' => 'Pavimentação, drenagem, acessos e redes de apoio à operação do posto.'],
                    ['title' => 'Obras civis do complexo', 'text' => 'Loja de conveniência, edificações de apoio, alvenaria e acabamentos do conjunto.'],
                ] as $service)
                    <article class="rounded-3xl border border-white/10 bg-white/[0.03] p-6 transition hover:border-amber-500/40 hover:bg-white/[0.05]">
                        <div class="mb-5 h-1 w-10 rounded-full bg-amber-500"></div>
                        <h3 class="text-lg font-black text-white">{{ $service['title'] }}</h3>
                        <p class="mt-3 text-sm leading-relaxed text-slate-400">{{ $service['text'] }}</p>
                    </article>
                @endforeach
            </div>
        </section>

        <section id="sobre" class="border-y border-white/10 bg-[#0b1220]">
            <div class="mx-auto grid max-w-7xl items-center gap-12 px-4 py-24 sm:px-6 lg:grid-cols-2 lg:px-8">
                <div class="relative overflow-hidden rounded-[2rem] border border-white/10">
                    @if($about)
                        <img src="{{ asset($about) }}" alt="Execução de posto de combustível Construtec" class="h-[34rem] w-full object-cover">
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-[#070b14] via-transparent to-transparent"></div>
                    <p class="absolute bottom-6 left-6 text-[10px] font-black uppercase tracking-[0.28em] text-amber-500">Construtec · Manaus</p>
                </div>
                <div>
                    <p class="text-[11px] font-black uppercase tracking-[0.32em] text-amber-500">A empresa</p>
                    <h2 class="mt-3 text-3xl font-black tracking-tight text-white sm:text-4xl">Especialistas na construção de postos.</h2>
                    <p class="mt-5 text-slate-400 leading-relaxed">
                        A Construtec atua em Manaus na construção de postos de combustível: engenharia, execução civil, estruturas metálicas e infraestrutura do complexo. Cada obra segue planejamento, controle técnico e cronograma de entrega.
                    </p>
                    <p class="mt-4 text-slate-400 leading-relaxed">
                        Clientes acompanham o andamento da obra pela área restrita, com etapas, registros de campo e documentação do contrato.
                    </p>
                    <a href="{{ $ctaHref }}" class="mt-8 inline-flex rounded-full bg-amber-500 px-6 py-3 text-[11px] font-black uppercase tracking-[0.18em] text-slate-900 transition hover:bg-amber-400">
                        {{ $ctaLabel }}
                    </a>
                </div>
            </div>
        </section>

        <section id="obras" class="mx-auto max-w-7xl px-4 py-24 sm:px-6 lg:px-8">
            <div class="flex flex-col justify-between gap-6 sm:flex-row sm:items-end">
                <div>
                    <p class="text-[11px] font-black uppercase tracking-[0.32em] text-amber-500">Portfólio</p>
                    <h2 class="mt-3 text-3xl font-black tracking-tight text-white sm:text-4xl">Obras em andamento e entregues</h2>
                </div>
                <p class="max-w-md text-sm text-slate-400">Postos de combustível e infraestrutura em Manaus.</p>
            </div>

            @if($gallery->isNotEmpty())
                <div class="mt-12 columns-1 gap-4 sm:columns-2 lg:columns-3">
                    @foreach($gallery as $image)
                        <figure class="mb-4 break-inside-avoid overflow-hidden rounded-3xl border border-white/10 bg-slate-900">
                            <img src="{{ asset($image) }}" alt="Obra de posto Construtec" class="w-full object-cover transition duration-500 hover:scale-[1.03]" loading="lazy">
                        </figure>
                    @endforeach
                </div>
            @endif
        </section>

        <section id="contato" class="border-t border-white/10 bg-[#0b1220]">
            <div class="mx-auto grid max-w-7xl gap-12 px-4 py-24 sm:px-6 lg:grid-cols-[1.2fr_0.8fr] lg:px-8">
                <div>
                    <p class="text-[11px] font-black uppercase tracking-[0.32em] text-amber-500">Contato</p>
                    <h2 class="mt-3 text-3xl font-black tracking-tight text-white sm:text-4xl">Fale conosco sobre o seu projeto.</h2>
                    <p class="mt-4 max-w-xl text-slate-400">Entre em contato para apresentar o empreendimento ou acompanhe a obra pela área do cliente.</p>
                </div>
                <div class="space-y-4 rounded-[2rem] border border-white/10 bg-white/[0.03] p-8">
                    @if($city)
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-500">{{ $city }}</p>
                    @endif
                    @if($phone)
                        <a href="tel:{{ preg_replace('/\D+/', '', $phone) }}" class="block text-lg font-black text-white hover:text-amber-400">{{ $phone }}</a>
                    @endif
                    @if($whatsappDigits)
                        <a href="https://wa.me/{{ $whatsappDigits }}" class="block text-sm font-bold uppercase tracking-[0.16em] text-amber-500 hover:text-amber-400" target="_blank" rel="noopener">WhatsApp</a>
                    @endif
                    @if($email)
                        <a href="mailto:{{ $email }}" class="block text-sm text-slate-300 hover:text-white">{{ $email }}</a>
                    @endif
                    @if($instagram)
                        <a href="https://instagram.com/{{ $instagram }}" class="block text-sm text-slate-300 hover:text-white" target="_blank" rel="noopener">@{{ $instagram }}</a>
                    @endif
                    @unless($hasContact)
                        <p class="text-sm text-slate-400">Acesse a área do cliente para acompanhar a obra.</p>
                    @endunless
                    <a href="{{ $ctaHref }}" class="mt-6 inline-flex w-full justify-center rounded-2xl bg-amber-500 px-6 py-3 text-[11px] font-black uppercase tracking-[0.18em] text-slate-900 transition hover:bg-amber-400">
                        {{ $ctaLabel }}
                    </a>
                </div>
            </div>
        </section>

        <footer class="border-t border-white/10">
            <div class="mx-auto flex max-w-7xl flex-col items-start justify-between gap-4 px-4 py-8 text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500 sm:flex-row sm:items-center sm:px-6 lg:px-8">
                <p>&copy; {{ date('Y') }} Construtec. Todos os direitos reservados.</p>
                <a href="{{ $ctaHref }}" class="text-amber-500 hover:text-amber-400">{{ $ctaLabel }}</a>
            </div>
        </footer>
    </div>
</x-site-layout>
