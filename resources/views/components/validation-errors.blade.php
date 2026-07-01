@if ($errors->any())
    <div class="fixed top-20 left-4 right-4 sm:left-auto sm:right-4 sm:max-w-md z-[100] p-4 bg-amber-500/20 border border-amber-500/50 rounded-2xl text-amber-200 text-sm backdrop-blur-xl shadow-2xl"
        x-data="{ show: true }"
        x-show="show"
        x-init="setTimeout(() => show = false, 8000)"
    >
        <p class="font-black uppercase tracking-widest text-[10px] mb-2 text-amber-400">Verifique os campos</p>
        <ul class="space-y-1 list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
