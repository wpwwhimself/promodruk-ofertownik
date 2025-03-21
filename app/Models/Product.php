<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = "string";

    protected $fillable = [
        "id",
        "product_family_id",
        "visible",
        "name",
        "description",
        "color",
        "sizes",
        "extra_description",
        "images",
        "thumbnails",
        "attributes",
        "original_sku",
        "price",
        "tabs",
        "related_product_ids",
    ];

    protected $casts = [
        "images" => "json",
        "thumbnails" => "json",
        "attributes" => "json",
        "color" => "json",
        "sizes" => "json",
        "tabs" => "json",
    ];

    private function sortByName($first, $second)
    {
        return Str::beforeLast(Str::afterLast($first, "/"), ".") <=> Str::beforeLast(Str::afterLast($second, "/"), ".");
    }
    protected function images(): Attribute
    {
        return Attribute::make(fn ($value) => collect(json_decode($value))
            // ->sort(fn ($a, $b) => $this->sortByName($a, $b))
            ->values()
        );
    }
    protected function thumbnails(): Attribute
    {
        return Attribute::make(fn ($value) => collect($this->images)
            // ->sortKeys()
            ->map(fn ($img, $i) => json_decode($value)[$i] ?? $img)
            // ->sort(fn ($a, $b) => $this->sortByName($a, $b))
            ->values()
        );
    }

    protected $appends = [
        "family",
    ];

    public function getMagazynDataAttribute()
    {
        return Http::get(env("MAGAZYN_API_URL") . "products/" . $this->id)->collect();
    }
    public function getFamilyAttribute()
    {
        return Product::where("product_family_id", $this->product_family_id)->get();
    }
    public function getFamilyVariantsListAttribute()
    {
        $family = $this->family;
        $colors = $family->pluck("color")->unique();
        return $colors;
    }
    public function getSimilarAttribute()
    {
        $data = collect();

        foreach ($this->categories as $category) {
            $data = $data->merge($category->products);
        }

        return $data;
    }
    public function getRelatedAttribute()
    {
        return (empty($this->related_product_ids))
            ? collect([])
            : Product::whereIn("id", explode(";", $this->related_product_ids))
                ->orWhereIn("product_family_id", explode(";", $this->related_product_ids))
                ->orderBy("id")
                ->get()
                ->groupBy("product_family_id")
                ->map(fn ($group) => $group->random());
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class)
            ->where("visible", ">=", Auth::id() ? 1 : 2);
    }
}
