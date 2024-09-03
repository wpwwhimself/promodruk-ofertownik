<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <style>
    @import url('https://fonts.cdnfonts.com/css/amazon-ember');

    body{
        font-family: "Amazon Ember", sans-serif;
        font-size: 16px;
    }
    td span {
        display: block;
    }
    table {
        border: 1px solid lightgray;
        border-collapse: collapse;

        td, th {
            border: 1px solid lightgray;
            padding: 0.5em;
        }
    }

    .logo {
        max-height: 5em;

        &.small {
            max-height: 2em;
        }
    }

    .thumbnail {
        max-height: 3em;
    }
    </style>
    <title>@yield("title") | {{ config("app.name") }}</title>
</head>
<body>
    <header>
        <x-logo />
        <h1>@yield("title")</h1>
    </header>

    @yield("content")

    <footer>
        <p>Pozdrawiamy,</p>
        <h2>Zespół promovera.pl</h2>

        <small>
            Wiadomość została wysłana systemowo stąd prosimy na nią NIE ODPOWIADAĆ.
            W przypadku kontaktu prosimy o skorzystania z danych kontaktowych zawartych <a href="http://promovera.pl/index/category/22">TUTAJ</a>.
            W związku z wprowadzeniem postanowień dot. RODO, po 25 maja 2018 zmieniliśmy zasady przetwarzania danych osobowych.
            Szczegóły (tj. m.in. kto jest administratorem danych osobowych, w jakim celu oraz w jaki sposób są przetwarzane, a także jak je zmienić lub usunąć) znajdują <a href="http://dok.promodruk.com.pl/rodo.pdf">tutaj</a>.
        </small>
    </footer>
</body>
</html>
