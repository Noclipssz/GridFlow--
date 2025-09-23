@props(['class' => ''])
<div {{ $attributes->class(['surface', $class]) }}>
    {{ $slot }}
</div>
