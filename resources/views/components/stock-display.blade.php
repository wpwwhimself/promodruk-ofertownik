@props([
    "productId",
    "long" => false,
])

<div class="stock-display">
    <h3>Aktualne stany magazynowe</h3>
    <table>
        <thead>
            <tr>
                @if ($long)
                <th>SKU</th>
                <th>Wariant</th>
                <th>Stan mag.</th>
                <th>Przewidywana dostawa</th>
                @else
                <th>Aktualny stan mag.</th>
                @endif
            </tr>
        </thead>
        <tbody class="data-table">
            <tr class="loader">
                <td colspan="{{ $long ? 4 : 1 }}">Ładowanie...</td>
            </tr>
        </tbody>
    </table>
</div>

<script defer>
fetch(`{{ env('MAGAZYN_API_URL') }}stock/{{ $productId }}`)
    .then(res => res.json())
    .then(data => {
        document.querySelector(".stock-display .loader").remove()

        data.data.forEach(row => {
            document.querySelector(".stock-display .data-table")
                .append(fromHTML(`<tr>
                    @if ($long)
                    <td><a href="{{ route('product') }}/${row.code}">${row.code}</a></td>
                    <td>${row.variant_name}</td>
                    <td><b>${row.quantity} szt.</b></td>
                    <td>${row.future_delivery}</td>
                    @else
                    <td>${row.quantity} szt.</td>
                    @endif
                </tr>`))
        })
    })
</script>