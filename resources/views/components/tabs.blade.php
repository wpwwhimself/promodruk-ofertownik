@props([
    "tabs",
])

@if ($tabs)

<div class="tabs flex-down">
    <nav class="flex-right">
        @foreach ($tabs as $tab)
        <li class="padded button-like animatable"
            onclick="switchToTab('{{ $tab['name'] }}')"
            data-tab-name="{{ $tab["name"] }}"
        >
            {{ $tab["name"] }}
        </li>
        @endforeach
    </nav>

    @foreach ($tabs as $tab)
    <div class="content-box flex-down hidden" data-tab-name="{{ $tab["name"] }}">
        @foreach ($tab["cells"] as $cell)
        @switch($cell["type"])
            @case("table")
                <table>
                    @if (isset($cell["headings"]))
                    <thead>
                        <tr>
                            @foreach ($cell["headings"] as $heading)
                            <th>{{ $heading }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    @endif
                    <tbody>
                        @foreach ($cell["content"] as $key => $value)
                        <tr>
                            <th>{{ $key }}</th>
                            <td>{!! str_replace("\n", "<br>", $value) !!}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @break
            @case("tiles")
                @foreach ($cell["content"] as $label => $link)
                <x-button :action="$link" target="_blank" :label="$label" icon="download" />
                @endforeach
                @break
            @default
            {{ $cell }}
        @endswitch
        @endforeach
    </div>
    @endforeach
</div>

<script>
const switchToTab = (tab_name) => {
    document.querySelectorAll(`.tabs > nav > li`).forEach(li => li.classList.remove("active"))
    document.querySelectorAll(`.tabs > .content-box`).forEach(box => box.classList.add("hidden"))

    document.querySelector(`.tabs > nav > li[data-tab-name="${tab_name}"]`).classList.add("active")
    document.querySelector(`.tabs > .content-box[data-tab-name="${tab_name}"]`).classList.remove("hidden")
}

// engage first tab
switchToTab(`{{ $tabs[0]["name"] }}`)
</script>

@endif
