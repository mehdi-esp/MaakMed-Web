import {Controller} from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['content']

    static values = { url: String }

    connect() {
    }

    async translate() {
        const content = this.contentTarget.textContent;

        const form = new FormData();
        form.append('content', content)

        const response = await fetch(this.urlValue, { method: 'POST', body: form });

        const json = await response.json();
        const { translation } = json;
        this.contentTarget.textContent = translation;
    }
}