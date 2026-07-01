@props([
    'url',
    'fileName' => 'comprovante',
    'title' => 'Comprovante',
    'text' => '',
    'label' => 'Comprovante',
])

<button
    type="button"
    {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/5 text-slate-400 rounded-lg text-[10px] font-bold uppercase tracking-widest hover:text-white hover:bg-white/10 transition-all disabled:opacity-50']) }}
    x-data="{ loading: false }"
    :disabled="loading"
    @click="
        loading = true;
        shareOrDownloadFile(@js($url), @js($fileName), @js($title), @js($text))
            .finally(() => loading = false);
    "
>
    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
    <span x-text="loading ? 'Abrindo...' : @js($label)"></span>
</button>
