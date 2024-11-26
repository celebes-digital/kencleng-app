<!doctype html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Kencleng Label</title>
</head>

<body style="margin: 24px 24px; padding-top: 12px;">
    <div style="text-align: start;">
        @foreach ($data as $item)
            <img src="{{ public_path('storage/' . $item['qr_image']) }}" alt="{{ $item['no_kencleng'] }}" style="width: 3cm; height: 3cm; border-width: 1px; border-color: #000; display: inline-block; break-after: page;" />
        @endforeach
    </div>
</body>
<style>
    @page {
        margin: 0px;
    }

    body {
        margin: 0px;
    }
</style>
</body>

</html>