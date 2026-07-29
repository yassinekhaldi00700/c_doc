@props(['height' => 36])

<img src="{{ asset('img/logo.png') }}"
     alt="{{ config('app.name') }}"
     {{ $attributes->merge(['style' => "height: {$height}px; width: auto;"]) }}>
