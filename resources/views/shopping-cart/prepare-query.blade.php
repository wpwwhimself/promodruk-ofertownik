@extends("layouts.main")
@section("title", "Przygotowanie zapytania")

@section("content")

<form action="{{ route('send-query') }}" method="post" enctype="multipart/form-data">
@csrf

<x-tiling count="2">
    <x-tiling.item title="Pozycje zapytania" icon="list">
        <x-tiling count="auto">
            @foreach ($cart as $item)
            <x-tiling.item
                :title="$item['product']->name"
                :subtitle="$item['product']->id"
                :img="collect($item['product']->thumbnails)->first()"
                :link="route('product', ['id' => $item['product']->id])"
            >
                <div>
                    @foreach ($item["attributes"] as ["attr" => $attr, "var" => $var])
                    <x-input-field type="dummy" name="" :label="$attr['name']" :value="$var['name']" disabled />
                    @endforeach
                    <x-input-field type="dummy" name="amounts[{{ $item['no'] }}]" label="Liczba szt." :value="$item['amount']" rows="2" disabled />
                    <x-input-field type="dummy" label="Komentarz" name="comments[{{ $item['no'] }}]" :value="$item['comment']" disabled />
                    <x-input-field type="dummy" name="attachments[{{ $item['no'] }}][]" label="Pliki projektu" :value="count(Storage::allFiles('public/attachments/temp/'.session()->get('_token').'/'.$item['no']))" disabled />
                </div>
            </x-tiling.item>
            @endforeach
        </x-tiling>
    </x-tiling.item>

    <x-tiling.item title="Dane kontaktowe" icon="user">
        <div>
            <x-input-field name="company_name" label="Firma" />
            <x-input-field name="first_name" label="Imię" />
            <x-input-field name="last_name" label="Nazwisko" />
            <x-input-field name="street_name" label="Ulica" />
            <x-input-field name="street_number" label="Numer budynku/lokalu" />
            <x-input-field name="zip_code" label="Kod pocztowy" />
            <x-input-field name="city" label="Miasto" />
            <x-input-field type="email" name="email_address" label="Adres e-mail" />
            <x-input-field type="tel" name="phone_number" label="Numer telefonu" />
        </div>
    </x-tiling.item>
</x-tiling>


<div class="flex-right center">
    <x-button :action="route('cart')" label="Wróć" icon="arrow-left" />
    <x-button action="submit" label="Wyslij zapytanie" icon="send" class="danger" />
</div>

</form>

@endsection