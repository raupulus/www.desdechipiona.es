@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-slate-400 focus:border-orange-700 focus:ring-orange-700 rounded-xl shadow-xs text-base lg:text-sm text-slate-950']) }}>
