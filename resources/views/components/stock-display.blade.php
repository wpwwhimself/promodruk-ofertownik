@if ($product->family->count() > 1)
<p>Wybierz kolor, aby zobaczyć zdjęcia i stan magazynowy</p>

<div class="flex-right wrap">
    @foreach ($product->family as $alt)
    <x-color-tag :color="collect($alt->color)"
        :active="$alt->id == $product->id"
        :link="route('product', ['id' => $alt->id])"
        pop="<span>{{ $alt->color['name'] }}: {{ $stockData->firstWhere('id', $alt->id)['current_stock'] ?? '-' }} szt. </span>"
    />
    @endforeach
</div>
@endif

<div class="stock-display">
    <span>
        <span style="margin-right: 2em">
            Kolor <a href="{{ route('product', ['id' => $product->id]) }}">{{ Str::lcfirst($product->color["name"]) }}</a>:
            <b>{{ $productStockData["current_stock"] }} szt.</b>
        </span>
        <span>
        Przewid. dost.: {{ $productStockData["future_delivery_amount"] ? "$productStockData[future_delivery_amount] szt., $productStockData[future_delivery_date]" : "brak" }}
        </span>
    </span>
</div>

<style>
.stock-display {
    margin-block: 0.5em;
}
</style>
