<?php

namespace App\Http\Controllers;

use App\Mail\Query;
use App\Mail\SendQueryConfirmed;
use App\Models\Product;
use App\Models\Supervisor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ShoppingCartController extends Controller
{
    private function getCart()
    {
        $attributes = Http::get(env("MAGAZYN_API_URL") . "attributes")->collect();

        $positions = collect(session("cart"))
            ->map(fn($item, $key) => [
                "no" => $key,
                "product" => Product::find($item["product_id"]),
                "attributes" => collect($item)
                    ->filter(fn($val, $key) => Str::startsWith($key, "attr-"))
                    ->map(function ($val, $key) use ($attributes, $item) {
                        $attr = $attributes->firstWhere("id", str_replace("attr-", "", $key));
                        $var = collect($attr["variants"])->firstWhere("id", $item[$key]);
                        return compact("attr", "var");
                    }),
                "comment" => $item["comment"],
                "amount" => $item["amount"],
                "attachments" => Storage::allFiles("public/attachments/temp/" . session()->get("_token") . "/" . $key),
            ]);
        $global_attachments = Storage::files("public/attachments/temp/" . session()->get("_token"));

        return collect(compact("positions", "global_attachments"));
    }

    public function index()
    {
        $cart = $this->getCart();
        $supervisors = Supervisor::where("visible", true)
            ->orderByRaw("case when email = 'biuro@promovera.pl' then 1 else 0 end")
            ->orderBy("name")
            ->get()
            ->mapWithKeys(fn($super) => ["$super->name ($super->email)" => $super->id]);

        return view("shopping-cart.index", compact(
            "cart",
            "supervisors",
        ));
    }

    public function add(Request $rq)
    {
        $next_no = $this->getCart()->count() == 0
            ? 0
            : $this->getCart()->max("no") + 1;

        $form_data = $rq->except(["_token", "files"]);
        $flattener = fn($val, $key) => !in_array($key, ["amount", "comment"]);
        if (collect($rq->session()->get("cart"))
            ->map(fn($item) => collect($item)->filter($flattener))
            ->contains(collect($form_data)->filter($flattener))
        ) {
            return back()->with("error", "Produkt jest już w koszyku");
        }
        $rq->session()->push("cart", $form_data);

        // temporary attachment storage
        if ($rq->file("files"))
        foreach ($rq->file("files") as $file) {
            $file->storePubliclyAs(
                "public/attachments/temp/".session()->get("_token")."/".$next_no,
                $file->getClientOriginalName()
            );
        }

        $product = Product::find($form_data["product_id"]);

        return back()->with("fullscreen-popup", [
            "content_up" => "Do zapytania został dodany produkt",
            "content_bold" => $product->name,
            "buttons" => [
                ["label" => "Kontynuuj zapytanie", "icon" => "arrow-right", "action" => route("product", $product->id)],
                ["label" => "Przejdź do podsumowania", "icon" => "cart", "action" => route("cart")],
            ]
        ]);
    }

    public function mod(Request $rq)
    {
        $cart = collect($rq->session()->pull("cart"));

        if ($rq->has("delete")) {
            $rq->session()->put("cart", $cart
                ->filter(fn($item, $key) => $key != $rq->input("delete"))
                ->toArray()
            );

            Storage::deleteDirectory("public/attachments/temp/" . session()->get("_token") . "/" . $rq->input("delete"));

            return back()->with("success", "Produkt został usunięty z koszyka");
        }

        $rq->session()->put("cart", $cart
            ->map(fn($item, $i) => [
                ...$item,
                "amount" => $rq->amounts[$i],
                "comment" => $rq->comments[$i],
            ])
            ->toArray()
        );

        // attachments
        foreach ($cart as $no => $item) {
            foreach (Storage::files("public/attachments/temp/" . session()->get("_token") . "/$no") as $file) {
                if (!in_array($file, explode(",", $rq->current_files[$no]))) {
                    Storage::delete($file);
                }
            }

            if (!isset($rq->file("files")[$no])) continue;
            foreach ($rq->file("files")[$no] as $file) {
                $file->storePubliclyAs(
                    "public/attachments/temp/".session()->get("_token")."/".$no,
                    $file->getClientOriginalName()
                );
            }
        }

        // global attachments
        foreach (Storage::files("public/attachments/temp/" . session()->get("_token")) as $file) {
            if (!in_array($file, explode(",", $rq->current_global_files))) {
                Storage::delete($file);
            }
        }

        foreach ($rq->file("global_files", []) as $file) {
            $file->storePubliclyAs(
                "public/attachments/temp/".session()->get("_token"),
                $file->getClientOriginalName()
            );
        }

        if ($rq->has("save")) return back()->with("success", "Koszyk został zapisany");

        return back()->with("success", "Koszyk został zaktualizowany");
    }

    public function sendQuery(Request $rq)
    {
        $cart = $this->getCart();
        $time = date("Y-m-d_H-i-s");

        // move attachments
        foreach (Storage::allFiles("public/attachments/temp/" . session()->get("_token")) as $file) {
            $file_path = Str::after($file, session()->get("_token") . "/");
            Storage::move($file, "public/attachments/$rq->email_address--$time/$file_path");
        }
        Storage::deleteDirectory("public/attachments/temp/".session()->get("_token"));

        $files = collect(Storage::allFiles("public/attachments/$rq->email_address--$time"))
            ->groupBy(fn ($file) => Str::beforeLast(Str::after($file, "public/attachments/$rq->email_address--$time/"), "/"));

        Mail::to(Supervisor::find($rq->supervisor_id)->email)
            ->send(new Query(
                $rq->except(["_token", "attachments"]),
                $cart["positions"],
                $files,
                $cart["global_attachments"]
            ));
        Mail::to($rq->email_address)
            ->send(new SendQueryConfirmed(
                $rq->except(["_token", "attachments"]),
                $cart["positions"],
                $files,
                $cart["global_attachments"]
            ));

        $rq->session()->pull("cart");

        return redirect()->route("home")->with("success", "Zapytanie zostało wysłane");
    }
}
