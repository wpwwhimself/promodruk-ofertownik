@extends("layouts.admin")
@section("title", "Strony górne")

@section("content")

{{ $pages->appends(compact("perPage", "sortBy"))->links("vendor.pagination.tailwind", [
    "availableSorts" => [
        'nazwa rosnąco' => 'name',
        'nazwa malejąco' => '-name',
    ],
]) }}

<x-tiling count="auto">
    @forelse ($pages as $page)
    <x-tiling.item :title="$page->name">
        <x-slot:buttons>
            <x-button
                :action="route('top-nav-pages-edit', ['id' => $page->id])"
                label="Edytuj"
                icon="tool"
            />
        </x-slot:buttons>
    </x-tiling.item>
    @empty
    <p class="ghost">Brak utworzonych stron</p>
    @endforelse
</x-tiling>

@endsection

@section("interactives")

<div class="flex-right center">
    <x-button :action="route('top-nav-pages-edit')" label="Nowa" icon="add" />
</div>

@endsection
