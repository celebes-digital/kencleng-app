<!-- @vite('resources/css/app.css') -->

<x-filament-panels::page>
    <div id="reader" width="600px">Cek</div>
</x-filament-panels::page>

@push('scripts')

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script type="text/javascript">
    // const div = document.getElementById('reader');
    // div.onclick = () => {
    //     console.log('Aku diklik');
    // }

    function onScanSuccess(decodedText, decodedResult) {
        // handle the scanned code as you like, for example:
        console.log(`Code matched = ${decodedText}`, decodedResult);
    }

    function onScanFailure(error) {
        // handle scan failure, usually better to ignore and keep scanning.
        // for example:
        console.warn(`Code scan error = ${error}`);
    }

    let html5QrcodeScanner = new Html5QrcodeScanner(
        "reader", {
            facingMode: 'environment'
        }, {
            fps: 10,
            qrbox: {
                width: 250,
                height: 250
            }
        },
        /* verbose= */
        false);
    html5QrcodeScanner.render(onScanSuccess, onScanFailure);
</script>
@endpush