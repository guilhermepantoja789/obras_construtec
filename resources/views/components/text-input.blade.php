@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'bg-white/5 border-white/10 dark:bg-slate-900/50 dark:border-white/10 focus:border-amber-500 focus:ring-amber-500 rounded-xl shadow-sm text-white placeholder-slate-500 backdrop-blur-sm']) }}>
