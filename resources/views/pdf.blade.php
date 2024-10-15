<!doctype html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Kencleng Label</title>
</head>

<body>
    <div style="text-align: center; width: 100%;">
        <div style="display: inline-block; width: 50%;">
            <h2>Batch 1 ({{count($data)}} Kencleng)</h2>
        </div>
    </div>
    <div class="margin-top" style="margin-top: 10px;">
        <table class="products" style="width: 100%; border-collapse: collapse;">
            @foreach($data as $item)
            @if ($loop->first || $loop->iteration % 7 == 0)
            <tr class="items" style="border: 1px solid black;">
                @endif
                <td style="border: 1px solid black; width: 16.66%; height: 3cm; text-align: center; vertical-align: middle;">
                    <img src="{{ public_path('storage/' . $item['qr_image']) }}" alt="{{ $item['no_kencleng'] }}" style="width: 3cm; height: 3cm;" />
                </td>
                @if ($loop->iteration % 6 == 0)
            </tr>
            @endif
            @endforeach
        </table>
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