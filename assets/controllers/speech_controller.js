import {Controller} from '@hotwired/stimulus';
import {useDebounce, useThrottle} from "stimulus-use";

/*
 * This is an example Stimulus controller!
 *
 * Any element with a data-controller="hello" attribute will cause
 * this controller to be executed. The name "hello" comes from the filename:
 * hello_controller.js -> "hello"
 *
 * Delete this file or adapt it for your use!
 */
export default class extends Controller {

    static values = {route: String};
    static targets = ["loading", "button"];

    static debounces = [
        {
            name: "playInstructionSound",
            wait: 500,
        }
    ];
    static throttles = [
        {
            name: "playInstructionSound",
            wait: 3000,
        }
    ];

    connect() {
        useDebounce(this);
        useThrottle(this);
    }

    playInstructionSound() {
        this.loadingTarget.classList.remove("hidden");
        this.buttonTarget.disabled = true;
        fetch(this.routeValue)
            .then(response => response.blob())
            .then(blob => {
                const audioUrl = URL.createObjectURL(blob);
                const audio = new Audio(audioUrl);
                audio.play().then(() => {
                    this.loadingTarget.classList.add("hidden");
                    this.buttonTarget.disabled = false;
                });
            });
    }
}
