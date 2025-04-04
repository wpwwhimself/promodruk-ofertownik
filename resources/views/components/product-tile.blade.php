@props([
    "product" => null,
    "productFamily" => null,
    "admin" => false,
    "ghost" => false,
])

@php
$showcased = $product ?? $productFamily->random();
$product ??= $productFamily->sortBy("price")->first();
$productFamily ??= $product->family;
@endphp

<x-tiling.item :title="Str::limit($product->name, 40)"
    :small-title="$product->family_prefixed_id"
    :subtitle="asPln($product->price)"
    :img="collect($showcased->thumbnails)->first() ?? collect($showcased->images)->first()"
    show-img-placeholder
    :link="$admin
        ? route('products-edit', ['id' => $product->family_prefixed_id])
        : route('product', ['id' => $product->front_id])"
    :ghost="$ghost"
>
    <span class="flex-right middle wrap">
        @if ($productFamily->count() > 0)

        @php
        $colors = $product->family_variants_list;
        @endphp
        @foreach (
            collect($colors)
                ->filter(fn ($clr) => request("filters.color")
                    ? ((request("filters.color") == "pozostałe")
                        ? $clr["color"] == null
                        : Str::contains($clr["name"], request("filters.color"))
                    )
                    : true
                )
                ->map(fn ($clr) => ["type" => "color", "var" => $clr])
        as $i => $var)
            @if ($i >= 28) <x-ik-ellypsis height="1em" /> @break @endif

            @if ($var["type"] == "color")
                <x-color-tag :color="collect($var['var'])" class="small" />
            @else
                <x-size-tag :size="$var['var']" class="small" />
            @endif
        @endforeach

        @endif
    </span>
</x-tiling.item>
