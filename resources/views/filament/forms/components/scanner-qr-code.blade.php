<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field">
    <div id="reader" wire:key="qr-code-scanner-reader">
        <div id="anim"></div>
    </div>
</x-dynamic-component>
@assets
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
@endassets
@script
<script type="text/javascript">
    let html5QrCode;
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
            const devices = await Html5Qrcode.getCameras();
            if (devices && devices.length) {
                const cameraId = devices[0].id;
                html5QrCode = new Html5Qrcode("reader");
                await html5QrCode.start(
                    cameraId,
                    config,
                    qrCodeMessage => {
                        console.log(`QR Code detected: ${qrCodeMessage}`);
                        $wire.set('data.kencleng_id', qrCodeMessage, true).then(() => {
                            // Pause scanning for 3 seconds
                            html5QrCode.pause(true);
                            setTimeout(() => {
                                html5QrCode.resume();
                            }, 3000);
                        });
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
        }
    }

    const stopQrCodeScanner = async () => {
        if (html5QrCode && html5QrCode.isScanning) {
            try {
                await html5QrCode.stop();
                console.log('QR Code scanner stopped');
            } catch (err) {
                console.log('Error stopping QR Code scanner', err);
            }
        }
    }

    // Start the scanner when the component is mounted
    $wire.on('component-mounted', () => {
        console.log('QR Code Scanner component mounted');
        if (!document.getElementById('reader').querySelector('.div-animation')) {
            startQrCodeScanner();
        }
    });

    // Stop the scanner when the component is unmounted or page is changed
    $wire.on('component-unmounted', stopQrCodeScanner);
    window.addEventListener('beforeunload', stopQrCodeScanner);

    // Reinitialize the scanner when Livewire updates the DOM
    document.addEventListener('livewire:navigated', () => {
        stopQrCodeScanner().then(() => {
            if (document.getElementById('reader') && !document.getElementById('reader').querySelector('.div-animation')) {
                startQrCodeScanner();
            }
        });
    });
</script>
@endscript