@extends("layouts.main")
@section("title", $category->name)
@section("subtitle", "Produkty")

@section("before-title")
<x-breadcrumbs :category="$category" />
@endsection

@section("content")

@if ($category->children->count())
<h2>Podkategorie</h2>
<x-tiling count="5" class="large-gap">
    @foreach ($category->children as $cat)
    <x-tiling.item :title="$cat->name"
        :img="$cat->thumbnail_link"
        :link="$cat->external_link ?? route('category-'.$cat->id)"
        show-img-placeholder
        image-covering
    >
        {{ \Illuminate\Mail\Markdown::parse($cat->description ?? "") }}

        <x-slot:buttons>
            <x-button action="none" label="Szczegóły" icon="chevrons-right" class="small" />
        </x-slot:buttons>
    </x-tiling.item>
    @endforeach
</x-tiling>

@else

<x-tiling count="auto">
    @forelse ($products as $product)
    <x-product-tile :product="$product" />
    @empty
    <p class="ghost">Brak produktów w tej kategorii</p>
    @endforelse
</x-tiling>

@endif

@endsection

@section("interactives")

@if ($category->children->count() == 0)
{{ $products->appends(compact("perPage", "sortBy", "filters"))->links("vendor.pagination.tailwind", [
    "availableFilters" => [
        "Kolor" => $colorsForFiltering,
    ]
]) }}
@endif

@endsection
