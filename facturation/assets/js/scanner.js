let activeReader = null;
let currentStreamControls = null;

function setScannerStatus(message) {
    const status = document.getElementById('scanner-status');
    if (status) {
        status.textContent = message;
    }
}

async function loadCameraDevices() {
    const select = document.getElementById('scanner-device');
    if (!select || !window.ZXing) {
        return [];
    }

    const tempReader = new ZXing.BrowserMultiFormatReader();
    const devices = await tempReader.listVideoInputDevices();
    select.innerHTML = '';

    devices.forEach((device, index) => {
        const option = document.createElement('option');
        option.value = device.deviceId;
        option.textContent = device.label || ('Camera ' + (index + 1));
        select.appendChild(option);
    });

    if (!devices.length) {
        const option = document.createElement('option');
        option.value = '';
        option.textContent = 'Aucune camera detectee';
        select.appendChild(option);
    }

    return devices;
}

function onBarcodeDetected(barcode) {
    const input = document.querySelector('input[name="code_barre"]');
    if (!input) {
        return;
    }
    input.value = barcode;
    input.dispatchEvent(new Event('change', { bubbles: true }));
}

async function startBarcodeScanner() {
    const video = document.getElementById('scanner-video');
    const select = document.getElementById('scanner-device');
    if (!video || !window.ZXing) {
        return;
    }

    try {
        stopBarcodeScanner();
        activeReader = new ZXing.BrowserMultiFormatReader();
        const selectedDeviceId = select && select.value ? select.value : null;
        setScannerStatus('Activation de la camera...');

        currentStreamControls = await activeReader.decodeFromVideoDevice(selectedDeviceId, video, (result, err) => {
            if (result) {
                onBarcodeDetected(result.getText());
                setScannerStatus('Code detecte: ' + result.getText());
                stopBarcodeScanner();
            } else if (err && !(err instanceof ZXing.NotFoundException)) {
                setScannerStatus('Erreur scan: ' + err);
            }
        });
        setScannerStatus('Camera active. Place le code-barres devant la webcam.');
    } catch (error) {
        setScannerStatus('Impossible d activer la camera. Autorise la webcam dans le navigateur.');
    }
}

function stopBarcodeScanner() {
    if (currentStreamControls && typeof currentStreamControls.stop === 'function') {
        currentStreamControls.stop();
        currentStreamControls = null;
    }
    if (activeReader) {
        activeReader.reset();
        activeReader = null;
    }
    setScannerStatus('Camera arretee.');
}

document.addEventListener('DOMContentLoaded', function () {
    const startBtn = document.getElementById('start-scan-btn');
    const stopBtn = document.getElementById('stop-scan-btn');
    const refreshBtn = document.getElementById('refresh-cameras-btn');
    if (startBtn) {
        startBtn.addEventListener('click', startBarcodeScanner);
    }
    if (stopBtn) {
        stopBtn.addEventListener('click', stopBarcodeScanner);
    }
    if (refreshBtn) {
        refreshBtn.addEventListener('click', loadCameraDevices);
    }
    if (document.getElementById('scanner-device')) {
        loadCameraDevices().catch(function () {
            setScannerStatus('Impossible de lister les cameras.');
        });
    }
});
