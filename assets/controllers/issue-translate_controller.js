import {Controller} from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['content']

    static values = { token: String }

    connect() {
    }

    async translate() {
        const url = 'https://api-inference.huggingface.co/models/Helsinki-NLP/opus-mt-en-fr';
        const token = '***REMOVED***';

        const content = this.contentTarget.textContent;
        const response = await fetch(url, {
            method: 'POST', headers: {
                'Content-Type': 'application/json', 'Authorization': `Bearer ${this.tokenValue}`
            }, body: JSON.stringify({inputs: content})
        });
        const json = await response.json();
        console.log(json);
        const translation = json[0].translation_text;
        this.contentTarget.textContent = translation;
    }
}