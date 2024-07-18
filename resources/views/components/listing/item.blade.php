@props([
    "title",
    "icon" => null,
])

<li class="flex-right center spread padded">
    <h3 class="flex-right center-both">
        @if ($icon) {{ svg(("ik-".$icon)) }} @endif
        {{ $title }}
    </h3>

    {{ $slot }}

    <div class="actions flex-right center-both">
        {{ $buttons }}
    </div>
</li>
