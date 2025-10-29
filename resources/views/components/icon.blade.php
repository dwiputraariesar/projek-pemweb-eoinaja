@props(['name','class' => 'h-5 w-5 text-current'])

@switch($name)
    @case('check')
        <svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 00-1.414-1.414L7 12.172 4.707 9.879A1 1 0 103.293 11.293l3 3a1 1 0 001.414 0l9-9z" clip-rule="evenodd"/></svg>
        @break
    @case('download')
        <svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M3 14a1 1 0 011-1h4v2H5v1h10v-1h-3v-2h4a1 1 0 011 1v3a1 1 0 01-1 1H4a1 1 0 01-1-1v-3z"/><path d="M7 7a1 1 0 012 0v4h2V7a1 1 0 112 0v4h2l-5 5-5-5h2V7z"/></svg>
        @break
    @case('eye')
        <svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10 3C6 3 2.73 5.11 1 8c1.73 2.89 5 5 9 5s7.27-2.11 9-5c-1.73-2.89-5-5-9-5zM10 11a3 3 0 100-6 3 3 0 000 6z"/></svg>
        @break
    @case('user')
        <svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10 10a4 4 0 100-8 4 4 0 000 8zm0 2c-5 0-8 2.5-8 5v1h16v-1c0-2.5-3-5-8-5z"/></svg>
        @break
    @case('filter')
        <svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M3 5a1 1 0 011-1h12a1 1 0 01.707 1.707L12 10.414V15a1 1 0 01-1.447.894L7 14.118V10.414L3.293 6.707A1 1 0 013 5z"/></svg>
        @break
    @case('csv')
        <svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M4 3h8l4 4v10a1 1 0 01-1 1H4a1 1 0 01-1-1V4a1 1 0 011-1z"/><path d="M8 7h5v2H8V7zM8 10h5v2H8v-2zM8 13h5v2H8v-2z"/></svg>
        @break
    @case('checkin')
        <svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M2 5a2 2 0 012-2h12a2 2 0 012 2v5h-2V5H4v10h6v2H4a2 2 0 01-2-2V5z"/><path d="M12.293 9.293a1 1 0 011.414 0L17 12.586l-1.707 1.707-3.293-3.293a1 1 0 010-1.414z"/></svg>
        @break
    @default
        <svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><circle cx="10" cy="10" r="8"/></svg>
@endswitch
