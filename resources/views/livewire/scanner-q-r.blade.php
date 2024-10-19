<div class="grid-cols gap-4">
    <div id="reader">
        <div id="anim"></div>
    </div>
    {{$this->form}}
</div>

@push('styles')
<style>
    /* https://drive.google.com/file/d/1Gs0mAJWim9zwyxOMO3XYWc6DEBLLL1Nj/view?pli=1 */
    #reader {
        background-color: antiquewhite;
        border-radius: 12px;
        overflow: hidden;
    }

    .div-animation {
        position: absolute;
        top: 56.5px;
        left: 125.5px;
        right: 125.5px;
        height: 5px;
        z-index: 100;
        background-color: aqua;
        animation: animate 4s ease-in-out infinite;
    }

    @keyframes animate {
        0% {
            top: 56.5px;
        }

        50% {
            top: calc(100% - 65.5px);
        }

        100% {
            top: 56.5px;
        }
    }
</style>
@endpush

@assets
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
@endassets

@script
<script type="text/javascript">
    console.log('QR Code Scanner script loaded');

    const config = {
        fps: 10000,
        qrbox: {
            width: 300,
            height: 300
        }
    };

    const startQrCodeScanner = async () => {
        try {
            console.log('Getting cameras');
            const resultQr = document.getElementById('data.kencleng_id');
            const devices = await Html5Qrcode.getCameras();
            if (devices && devices.length) {
                const cameraId = devices[0].id;
                const html5Qrcode = new Html5Qrcode("reader");

                await html5Qrcode.start(
                    cameraId,
                    config,
                    qrCodeMessage => {
                        // resultQr.value = qrCodeMessage;
                        console.log(`QR Code detected: ${qrCodeMessage}`);
                        // @this->set('data')
                        // $wire.scanQr(qrCodeMessage);

                        // console.log($wire);
                        // console.log($wire.$model('data'));
                        // console.log($wire.$model('data', 'nasi'));
                        // console.log($wire.$model.$live);

                        // $wire.$set('data', [qrCodeMessage]);
                        // $wire.$set('{$component->getStatePath(true)}', qrCodeMessage);

                        html5Qrcode.pause(true)
                        console.log('QR Code paused');
                    },
                    errorMessage => {
                        console.log(`QR Code Error: ${errorMessage}`);
                    }
                );

                const overlayElement = document.getElementById('reader');

                let divAnimation = document.createElement("div");
                divAnimation.classList.add('div-animation');

                overlayElement.appendChild(divAnimation);
                console.log('QR Code started');
            }
        } catch (err) {
            console.log('Error getting cameras or starting QR Code', err);
            startQrCodeScanner();
        }
    }

    // document.getElementById('reader') ?
    //     startQrCodeScanner() : '';
    startQrCodeScanner();
</script>
@endscript