<div id="reader">
    <div id="anim"></div>
</div>


@push('styles')
<style>
    /* https://drive.google.com/file/d/1Gs0mAJWim9zwyxOMO3XYWc6DEBLLL1Nj/view?pli=1 */
    #reader {
        width: 100%;
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

    /* .div-animation {
        width: 100%;
        height: 100%;
        display: flex;
        position: absolute;
        top: 0;
        z-index: 100;
        justify-content: center;
        align-items: center;
        background-color: aqua;
    } */
</style>
@endpush

@assets
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
@endassets

@script

<script type="text/javascript">
    Html5Qrcode.getCameras().then(devices => {
        if (devices && devices.length) {
            const cameraId = devices[0].id;
            const html5Qrcode = new Html5Qrcode("reader");

            html5Qrcode.start(
                    cameraId, {
                        fps: 10,
                        qrbox: {
                            width: 300,
                            height: 300
                        }
                    },
                    qrCodeMessage => {
                        console.log(`QR Code detected: ${qrCodeMessage}`);
                        $wire.updateDataQR(qrCodeMessage);
                        console.log(`Finish wire`);
                        html5Qrcode.pause(true).then(ignore => {
                            console.log('QR Code stopped');
                        }).catch(err => {
                            console.log('Error stopping QR Code', err);
                        });
                    },
                    errorMessage => {
                        console.log(`QR Code Error: ${errorMessage}`);
                    })
                .then(() => {
                    console.log('Me here')
                    const overlayElement = document.getElementById('reader');
                    console.log(overlayElement)

                    let divAnimation = document.createElement("div");
                    divAnimation.classList.add('div-animation');

                    overlayElement.appendChild(divAnimation);
                    console.log('QR Code started');
                })
                .catch(err => {
                    console.log('Error starting QR Code', err);
                });
        }
    }).catch(err => {
        console.log('Error getting cameras', err);
    });
</script>
@endscript