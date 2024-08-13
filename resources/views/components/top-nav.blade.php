@props([
    "pages",
    "withAllProducts" => false,
])

<nav id="top-nav" class="flex-right">
    @if ($withAllProducts)
    <x-button action="none" label="Wszystkie produkty" icon="hamburger" onclick="toggleCategoryDropdown()" class="all-products-btn" />
    <x-category-dropdown />
    @endif

    @foreach ($pages as [$label, $route])
    <a href="{{ route($route) }}"
        class="{{ Route::currentRouteName() == $route ? "active" : "" }} padded animatable flex-right middle"
    >
        {{ $label }}
    </a>
    @endforeach
</nav>

<script>
    const dropdown = document.getElementById("category-dropdown")
    const btn = document.querySelector(".all-products-btn")

    const toggleCategoryDropdown = () => {
        btn.classList.toggle("active")
        dropdown.classList.toggle("visible")
    }
    const hideCategoryDropdown = () => {
        btn.classList.remove("active")
        dropdown.classList.remove("visible")
    }

    window.onclick = (event) => {
        if (!event.target.matches("#category-dropdown li, .all-products-btn")) hideCategoryDropdown()
    }
</script>

<style>
.all-products-btn {
    font-weight: bold;
    padding-inline: 2em;
    margin-right: 3em;
}
</style>
