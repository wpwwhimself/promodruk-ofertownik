<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use App\Models\TopNavPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public static $pages = [
        ["Ogólne", "dashboard"],
        ["Strony górne", "top-nav-pages"],
        ["Kategorie", "categories"],
        ["Produkty", "products"],
    ];

    public static $updaters = [
        "settings",
        "logo",
        "welcome-text",
        "top-nav-pages",
        "categories",
        "products",
    ];

    /////////////// pages ////////////////

    public function dashboard()
    {
        $general_settings = Setting::where("group", "general")->get();
        [$welcome_text_content, $welcome_text_visible] = Setting::where("group", "welcome_text")->get();

        return view("admin.dashboard", compact(
            "general_settings",
            "welcome_text_content",
            "welcome_text_visible",
        ));
    }

    public function topNavPages()
    {
        $pages = TopNavPage::orderBy("ordering")->get();

        return view("admin.top-nav-pages", compact(
            "pages"
        ));
    }
    public function topNavPagesEdit(int $id = null)
    {
        $page = ($id) ? TopNavPage::findOrFail($id) : null;

        return view("admin.top-nav-page", compact(
            "page"
        ));
    }

    public function categories()
    {
        $categories = Category::all();

        return view("admin.categories", compact(
            "categories"
        ));
    }
    public function categoriesEdit(int $id = null)
    {
        $category = ($id) ? Category::findOrFail($id) : null;

        $parent_categories_available = Category::all()
            ->reject(fn ($cat) => $cat->id === $id);
        if ($category) {
            $parent_categories_available = $parent_categories_available->filter(
                fn ($cat) => !$category->all_children->contains(fn ($ccat) => $ccat->id == $cat->id)
            );
        }
        $parent_categories_available = $parent_categories_available->mapWithKeys(
            fn ($cat) => [str_repeat("- ", $cat->depth) . $cat->name => $cat->id]
        );

        return view("admin.category", compact(
            "category",
            "parent_categories_available",
        ));
    }

    public function products()
    {
        $products = Product::all();

        return view("admin.products", compact(
            "products",
        ));
    }
    public function productEdit(string $id = null)
    {
        $product = ($id) ? Product::findOrFail($id) : null;
        $all_categories = Category::all()
            ->sort(fn ($a, $b) => $a->breadcrumbs <=> $b->breadcrumbs);

        return view("admin.product", compact(
            "product",
            "all_categories",
        ));
    }

    /////////////// updaters ////////////////

    public function updateSettings(Request $rq)
    {
        foreach ($rq->except("_token") as $name => $value) {
            Setting::find($name)->update(["value" => $value]);
        };

        return redirect()->back()->with("success", "Ustawienia zostały zaktualizowane");
    }

    public function updateLogo(Request $rq)
    {
        if ($rq->file("logo")->extension() !== "png") {
            return redirect()->back()->with("error", "Logo musi mieć rozszerzenie .png");
        }

        if (!$rq->file("logo")->storeAs(
            "meta",
            "logo.".$rq->file("logo")->extension(),
            "public"
        )) {
            return redirect()->back()->with("error", "Logo nie zostało zaktualizowane");
        }

        return redirect()->back()->with("success", "Logo zostało zaktualizowane");
    }

    public function updateWelcomeText(Request $rq)
    {
        Setting::find("welcome_text_content")->update(["value" => $rq->welcome_text_content]);
        Setting::find("welcome_text_visible")->update(["value" => $rq->welcome_text_visible]);
        return redirect()->back()->with("success", "Tekst powitalny zaktualizowany");
    }

    public function updateTopNavPages(Request $rq)
    {
        $form_data = [
            "name" => $rq->name,
            "ordering" => $rq->ordering,
            "content" => $rq->content,
        ];

        if ($rq->mode == "save") {
            $page = (!$rq->id)
                ? TopNavPage::create($form_data)
                : TopNavPage::find($rq->id)->update($form_data);
            return redirect(route("top-nav-pages-edit", ["id" => $rq->id ?? $page->id]))->with("success", "Strona została zapisana");
        } else if ($rq->mode == "delete") {
            TopNavPage::find($rq->id)->delete();
            return redirect(route("top-nav-pages"))->with("success", "Strona została usunięta");
        } else {
            abort(400, "Updater mode is missing or incorrect");
        }
    }

    public function updateCategories(Request $rq)
    {
        $form_data = $rq->except(["_token", "mode", "id"]);
        foreach(["visible"] as $label) { // checkboxes
            $form_data[$label] = $rq->has($label);
        }

        if ($rq->mode == "save") {
            $category = (!$rq->id)
                ? Category::create($form_data)
                : Category::find($rq->id)->update($form_data);
            return redirect(route("categories-edit", ["id" => $rq->id ?? $category->id]))->with("success", "Kategoria została zapisana");
        } else if ($rq->mode == "delete") {
            Category::find($rq->id)->delete();
            return redirect(route("categories"))->with("success", "Kategoria została usunięta");
        } else {
            abort(400, "Updater mode is missing or incorrect");
        }
    }

    public function updateProducts(Request $rq)
    {
        $form_data = $rq->except(["_token", "mode"]);
        foreach(["visible"] as $label) { // checkboxes
            $form_data[$label] = $rq->has($label);
        }
        $categories = array_filter(explode(",", $form_data["categories"]));

        $magazyn_product = Http::get(env("MAGAZYN_API_URL") . "products/" . $rq->id);
        if ($magazyn_product->notFound()) {
            return back()->with("error", "Produkt o podanym SKU nie istnieje w Magazynie");
        }

        $magazyn_product = $magazyn_product->json();
        $form_data = array_merge($form_data, $magazyn_product);

        if ($rq->mode == "save") {
            $product = Product::updateOrCreate(["id" => $rq->id], $form_data);
            $product->categories()->sync($categories);
            return redirect(route("products-edit", ["id" => $rq->id ?? $product->id]))->with("success", "Produkt został zapisany");
        } else if ($rq->mode == "delete") {
            Product::find($rq->id)->delete();
            return redirect(route("products"))->with("success", "Produkt został usunięty");
        } else {
            abort(400, "Updater mode is missing or incorrect");
        }
    }
}
