<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field">
    <div wire:ignore id="reader" wire:key="qr-code-scanner-reader" class="rounded-md overflow-hidden">
        <div id="anim"></div>
    </div>
</x-dynamic-component>

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
        border-radius: 12px;
        background-color: aqua;
        animation: animate 4s ease-in-out infinite;
        box-shadow: 0 0 10px 2px rgba(0, 255, 255, 0.7);
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

@push('scripts')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
@endpush

@script
<script>
    // Global variables to manage scanner state
    window.qrScanner = window.qrScanner || {
        instance: null,
        isInitialized: false,
        initializationInProgress: false
    };

    const config = {
        fps: 100,
        qrbox: {
            width: 300,
            height: 300
        }
    };

    const startQrCodeScanner = async () => {
        if (window.qrScanner.isInitialized || window.qrScanner.initializationInProgress) {
            console.log('Scanner already initialized or initialization in progress');
            return;
        }

        window.qrScanner.initializationInProgress = true;

        try {
            console.log('Initializing QR scanner');
            const devices = await Html5Qrcode.getCameras();
            if (devices && devices.length) {
                const cameraId = devices.length < 2 ? devices[0].id : devices[1].id;
                window.qrScanner.instance = new Html5Qrcode("reader");
                await window.qrScanner.instance.start(
                    cameraId,
                    config,
                    handleQRCodeDetected,
                    errorMessage => console.log(`QR Code Error: ${errorMessage}`)
                );
                window.qrScanner.isInitialized = true;
                const overlayElement = document.getElementById('reader');
                if (overlayElement && !overlayElement.querySelector('.div-animation')) {
                    let divAnimation = document.createElement("div");
                    divAnimation.classList.add('div-animation');
                    overlayElement.appendChild(divAnimation);
                }
                console.log('QR Code scanner initialized');
            }
        } catch (err) {
            console.error('Error initializing QR Code scanner:', err);
        } finally {
            window.qrScanner.initializationInProgress = false;
        }
    }

    const handleQRCodeDetected = async (qrCodeMessage) => {
        console.log(`QR Code detected: ${qrCodeMessage}`);

        window.qrScanner.instance.pause(true);

        setTimeout(async () => {
            try {
                const isNiceKencleng = await $wire.call('checkNoKencleng', qrCodeMessage);
                console.log('isNiceKencleng ', isNiceKencleng);

                if (!isNiceKencleng && window.qrScanner.instance) {
                    setTimeout(() => {
                        if (window.qrScanner.instance) {
                            window.qrScanner.instance.resume();
                        }
                    }, 1000);
                }

                if (isNiceKencleng) {
                    console.log('QR Code message set to Livewire data');
                    $wire.set('data.scanner', qrCodeMessage, true);
                }
                console.log('END: isNiceKencleng ', isNiceKencleng);
            } catch (error) {
                console.error('Error during QR code handling:', error);
            }
        }, 1000);
    }

    const stopQrCodeScanner = async () => {
        if (window.qrScanner.instance) {
            try {
                if (window.qrScanner.instance.isScanning) {
                    await window.qrScanner.instance.stop();
                }
                window.qrScanner.instance.clear();
                window.qrScanner.instance = null;
                window.qrScanner.isInitialized = false;
                console.log('QR Code scanner stopped and reset');
            } catch (err) {
                console.error('Error stopping QR Code scanner:', err);
            }
        }
    }

    const reinitializeScanner = () => {
        console.log('Reinitializing scanner');
        stopQrCodeScanner().then(() => {
            if (document.getElementById('reader')) {
                startQrCodeScanner();
            }
        });
    }

    // Start the scanner when the component is mounted
    $wire.on('component-mounted', () => {
        console.log('Component mounted');
        reinitializeScanner();
    });

    // Clean up when the component is unmounted
    $wire.on('component-unmounted', () => {
        console.log('Component unmounted');
        stopQrCodeScanner();
    });

    // Handle Livewire navigation
    document.addEventListener('livewire:navigated', () => {
        console.log('Livewire navigated');
        if (document.getElementById('reader')) {
            reinitializeScanner();
        } else {
            stopQrCodeScanner();
        }
    });

    // Clean up on page unload
    window.addEventListener('beforeunload', stopQrCodeScanner);
</script>
@endscript