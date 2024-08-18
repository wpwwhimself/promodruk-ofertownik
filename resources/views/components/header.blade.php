<header class="padded">
    <div class="max-width-wrapper flex-right spread">
        <div class="flex-right middle">
            <x-logo />
        </div>

        <search>
            <form action="{{ route('search') }}" method="post" class="flex-right middle">
                @csrf
                <input id="query" type="text" placeholder="Wyszukaj produkty..." name="query" value="{{ Str::contains(request()->url(), "produkty/szukaj") ? Str::afterLast(request()->url(), "/") : "" }}" />
                <x-button action="submit" label="" icon="search" />
            </form>
        </search>

        <div class="flex-right">
            @php $cart_count = count(session('cart', [])); @endphp
            <x-button :action="route('cart')" :label="'Koszyk zapytań' . ($cart_count ? ' ('.$cart_count.')' : '')" icon="cart" icon-right />
        </div>
    </div>
</header>
