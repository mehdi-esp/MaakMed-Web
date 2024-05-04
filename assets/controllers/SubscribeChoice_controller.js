import { Controller } from '@hotwired/stimulus';
import QRCode from 'qrcode';


export default class extends Controller {
    connect() {
        let redirectButton = document.getElementById('redirect-button');
        let generateQrButton = document.getElementById('generate-qr-button');
        let planId;
        let planCost;

        function handleClick(callback) {
            document.addEventListener('click', function(event) {
                if (event.target.dataset.subscribeButton) {
                    planId = event.target.dataset.planId;
                    planCost = event.target.dataset.planCost;
                    callback(planId, planCost);
                }
            });
        }

        handleClick(function(planId, planCost) {
            let formActionUrl = "http://127.0.0.1:8000/subscription/subscribe";
            const form = new FormData();
            form.append('planId', planId);
            form.append('amount', planCost);
            function generateQrCode(url) {
                let canvas = document.getElementById('qr-code-canvas');

                QRCode.toCanvas(canvas, url, function (error) {
                    if (error) console.error(error);
                    let qrCodeModal = document.getElementById('qr_code_modal');
                    if (qrCodeModal) {
                        qrCodeModal.showModal();
                    }
                });
            }

            if (redirectButton) {
                redirectButton.addEventListener('click', function(event) {
                    event.preventDefault();
                    fetch(formActionUrl, { method: 'POST', body: form})
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            window.location.href = data.url;
                        });
                });
            }
            if (generateQrButton) {
                generateQrButton.addEventListener('click', function(event) {
                    try {
                        event.preventDefault();
                        let qrCodeModal = document.getElementById('qr_code_modal');
                        if (qrCodeModal) {

                            let canvas = document.createElement('canvas');
                            canvas.id = 'qr-code-canvas';

                            let oldCanvas = qrCodeModal.querySelector('#qr-code-canvas');
                            if (oldCanvas && oldCanvas.parentNode) {
                                oldCanvas.parentNode.removeChild(oldCanvas);
                            }

                            qrCodeModal.appendChild(canvas);
                            qrCodeModal.showModal();

                            fetch(formActionUrl, { method: 'POST', body: form })
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error(`HTTP error! status: ${response.status}`);
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    let sessionUrl = data.url;
                                    console.log(sessionUrl);
                                    if (sessionUrl) {
                                        generateQrCode(sessionUrl);
                                    } else {
                                        console.error('No URL found in the response text');
                                    }
                                });
                        }
                    } catch (error) {
                        console.log(error.message);
                        console.log(error.stack);
                    }
                });
            }
        });
        }
}