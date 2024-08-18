@props([
    "count" => 3
])

@if ($count == "auto")
<div {{ $attributes->class(["tiling", "flex-right", "wrap", "center"]) }}>
@else
<div {{ $attributes->class(["tiling", "grid"]) }} style="grid-template-columns: repeat({{ $count }}, var(--tile-width));">
@endif

    {{ $slot }}
</div>
