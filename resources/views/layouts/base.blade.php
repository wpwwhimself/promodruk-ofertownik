<!DOCTYPE html>
<html lang="en">
<x-layout.head />
<body class="flex-down center">
    <script>
    // categories for listings
    const categories = {!! json_encode(
        \App\Models\Category::with("children.children")
            ->where("visible", ">=", Auth::id() ? 1 : 2)
            ->orderBy("ordering")
            ->orderBy("name")
            ->get()
    ) !!}
    const revealInput = (name) => {
        document.querySelector(`[name="${name}"]`).classList.remove("hidden")
        document.querySelector(`[name="${name}"]`).closest(".input-container").classList.remove("hidden")
        document.querySelector(`.hidden-save`)?.classList.remove("hidden")
        document.querySelector(`.input-container[for="${name}"]`).classList.add("hidden")
    }
    const submitNearestForm = (element) => {
        element.closest("form").submit()
    }
    </script>

    <div id="header-wrapper" class="flex-down animatable">
        <x-header />
        <x-top-nav
            :pages="\App\Models\TopNavPage::ordered()
                ->where('show_in_top_nav', true)
                ->get()
                ->map(fn ($page) => [$page->name, $page->slug])"
            with-all-products
        />
    </div>

    @yield("before-main")

    <div id="main-wrapper" class="max-width-wrapper">
        <div id="sidebar-wrapper" class="grid">
            @yield("insides")
        </div>
    </div>
    <x-footer />

    @foreach (["success", "error"] as $status)
    @if (session($status))
    <x-popup-alert :status="$status" />
    @endif
    @endforeach

    @if (session("fullscreen-popup"))
    <x-fullscreen-popup :data="session('fullscreen-popup')" />
    @endif

    @bukScripts(true)
</body>
</html>
