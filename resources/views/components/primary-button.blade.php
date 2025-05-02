<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-[#00724F] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#005a3f] focus:bg-[#005a3f] active:bg-[#004530] focus:outline-none focus:ring-2 focus:ring-[#00724F] focus:ring-offset-2 transition ease-in-out duration-150 shadow-md hover:shadow-lg']) }}>
    {{ $slot }}
</button>
