@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-300 focus:border-[#00724F] focus:ring-[#00724F] rounded-md shadow-sm']) !!}>
