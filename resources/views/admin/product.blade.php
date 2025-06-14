@extends("layouts.admin")
@section("title", implode(" | ", [$product->family_name ?? "Nowy produkt", "Edycja produktu"]))

@section("content")

<form action="{{ route('update-products') }}" method="post" class="flex-down">
    @csrf
    <input type="hidden" name="id" value="{{ $product->product_family_id }}" />
    <input type="hidden" name="front_id" value="{{ $product->front_id }}">
    <input type="hidden" name="_family_prefixed_id" value="{{ $product->family_prefixed_id }}">

    <x-tiling count="2" class="stretch-tiles">
        <x-tiling.item title="Warianty" icon="copy">
            <div class="grid" style="grid-template-columns: 1fr 1fr">
                @foreach ($family as $variant)
                <span>
                    <img src="{{ $variant->thumbnails->first() }}" alt="{{ $variant->name }}" class="inline"
                        {{ Popper::pop("<img src='" . $variant->thumbnails->first() . "' />") }}
                    >
                    <a href="{{ route('product', ['id' => $variant->front_id]) }}" target="_blank">{{ $variant->front_id }}</a>
                    <x-color-tag :color="collect($variant->color)" :pop="$variant->color['name']" />

                    @if (count($variant->sizes ?? []) > 1)
                    <x-size-tag :size="collect($variant->sizes)->first()" /> - <x-size-tag :size="collect($variant->sizes)->last()" />
                    @elseif (count($variant->sizes ?? []) == 1)
                    <x-size-tag :size="collect($variant->sizes)->first()" />
                    @endif
                </span>
                @endforeach
            </div>

            <div class="flex-right center">
                <x-button :action="env('MAGAZYN_URL').'admin/products/edit-family/'.$product->family_prefixed_id" target="_blank" label="Edytuj w Magazynie" icon="box" />
            </div>
        </x-tiling.item>

        <x-tiling.item title="Ustawienia lokalne" icon="home">
            <x-multi-input-field label="Widoczny" name="visible" :value="$product?->visible ?? 2" :options="VISIBILITIES" />
            <x-input-field type="checkbox" name="hide_family_sku_on_listing" label="Ukryj SKU rodziny na listingu" :value="$product?->hide_family_sku_on_listing" />
            <x-ckeditor name="extra_description" label="Dodatkowy opis" :value="$product?->extra_description" />
        </x-tiling.item>

        <x-tiling.item title="Kategorie" icon="inbox">
            <x-category-selector :selected-categories="$product->categories" />
        </x-tiling.item>

        <x-tiling.item title="Powiązane produkty" icon="link">
            <p class="ghost">
                Wpisz SKU produktów, które mają być wyświetlane wspólnie z tym produktem.
                Pozycje rozdziel średnikiem.
            </p>

            <x-input-field type="text"
                name="related_product_ids"
                label="SKU powiązanych produktów"
                :value="$product->related_product_ids"
            />

            <ul>
                @forelse ($product->related as $product)
                <li>
                    <img src="{{ collect($product["thumbnails"])->first() }}" alt="{{ $product["name"] }}" class="inline"
                        {{ Popper::pop("<img src='" . collect($product["thumbnails"])->first() . "' />") }}
                    >
                    {{ $product["name"] }}
                </li>
                @empty
                <span class="ghost">Brak powiązanych produktów</span>
                @endforelse
            </ul>
        </x-tiling.item>
    </x-tiling>

    <div class="flex-right center">
        <x-button action="submit" name="mode" value="save" label="Zapisz" icon="save" />
        <x-button action="submit" name="mode" value="delete" label="Usuń" icon="delete" class="danger" />
    </div>
    <div class="flex-right center">
        <x-button :action="route('products')" label="Wróć" icon="arrow-left" />
    </div>
</form>

@endsection
