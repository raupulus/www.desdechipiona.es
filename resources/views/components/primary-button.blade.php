<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-5 py-2.5 bg-orange-700 border border-transparent rounded-xl font-extrabold text-xs text-white uppercase tracking-wider hover:bg-orange-800 focus:bg-orange-800 active:bg-slate-950 focus:outline-none focus:ring-2 focus:ring-orange-600 focus:ring-offset-2 transition ease-in-out duration-150 cursor-pointer']) }}>
    {{ $slot }}
</button>
