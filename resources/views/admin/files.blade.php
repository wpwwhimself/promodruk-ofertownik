@extends("layouts.admin")
@section("title", "Repozytorium plików")

@section("content")

<form action="{{ route('files-upload') }}" method="post" enctype="multipart/form-data"
    class="flex-right center"
>
    @csrf
    <input type="file" name="files" id="files" multiple>
    <x-button action="submit" label="Wgraj" icon="upload" />
</form>

<h2>{{ request("path", "public") }}</h2>

<div class="flex-right wrap">
    @if (!in_array(request("path"), ["public", null]))
    <x-button :action="route('files', ['path' => Str::beforeLast(request('path'), '/')])" label=".." icon="folder" />
    @endif

    @foreach ($directories as $dir)
    <x-button :action="route('files', ['path' => $dir])" :label="Str::afterLast($dir, '/')" icon="folder" />
    @endforeach
</div>

<x-tiling count="auto">
    @forelse ($files as $file)
    <x-tiling.item :title="Str::afterLast($file, '/')"
        :img="Storage::url($file)"
    >
    </x-tiling.item>
    @empty
    <p class="ghost">Brak plików</p>
    @endforelse
</x-tiling>

@endsection

@section("interactives")

{{-- {{ $files->links() }} --}}

<div class="flex-right center">

</div>

@endsection
