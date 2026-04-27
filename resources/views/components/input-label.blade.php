@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-bold text-xs uppercase tracking-widest text-slate-400 mb-2']) }}>
    {{ $value ?? $slot }}
</label>
