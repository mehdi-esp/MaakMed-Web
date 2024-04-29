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

                    // Call the callback function
                    callback(planId, planCost);
                }
            });
        }

// Use the handleClick function
        handleClick(function(planId, planCost) {
            console.log("new button events", planId, planCost);
            let formActionUrl = "http://127.0.0.1:8000/"+planId+"/"+planCost;
            function generateQrCode(url) {
                let canvas = document.getElementById('qr-code-canvas'); // Replace with the id of your canvas element

                QRCode.toCanvas(canvas, url, function (error) {
                    if (error) console.error(error);
                    console.log('QR code generated!');

                    // Show the QR code modal
                    let qrCodeModal = document.getElementById('qr_code_modal');
                    if (qrCodeModal) {
                        qrCodeModal.showModal();
                    }
                });
            }





            if (redirectButton) {
                redirectButton.addEventListener('click', function(event) {
                    event.preventDefault();
                    fetch(formActionUrl)
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

                        // Get the new dialog
                        let qrCodeModal = document.getElementById('qr_code_modal');
                        if (qrCodeModal) {
                            // Create a new canvas element for the QR code
                            let canvas = document.createElement('canvas');
                            canvas.id = 'qr-code-canvas';

                            // Remove old canvas if it exists
                            let oldCanvas = qrCodeModal.querySelector('#qr-code-canvas');
                            if (oldCanvas && oldCanvas.parentNode) {
                                oldCanvas.parentNode.removeChild(oldCanvas);
                            }

                            // Append the new canvas to the new dialog
                            qrCodeModal.appendChild(canvas);

                            // Show the new dialog
                            qrCodeModal.showModal();

                            fetch(formActionUrl)
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