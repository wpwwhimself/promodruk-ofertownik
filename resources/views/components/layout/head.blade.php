<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="icon" type="image/png" href="{{ File::exists("storage/meta/favicon.png") ? asset("storage/meta/favicon.png") : asset("storage/meta/logo.png") }}">

    <link rel="stylesheet" href="{{ asset("css/app.css") }}">
    <script defer src="{{ asset("js/app.js") }}"></script>
    {!! "<style>" !!}
    :root {
        @foreach (\App\Models\Setting::where("name", "like", "app\_accent\_color\__")->get() as $setting)
        --acc{{ substr($setting->name, -1) }}: {{ $setting->value }};
        @endforeach
    }
    {!! "</style>" !!}

    @bukStyles(true)

    <title>
        @yield("title") |
        @hasSection ("subtitle") @yield("subtitle") | @endif
        {{ getSetting("app_name") ?? "Ofertownik" }}
    </title>

    <script src="{{ asset("js/start.js") }}"></script>
</head>
