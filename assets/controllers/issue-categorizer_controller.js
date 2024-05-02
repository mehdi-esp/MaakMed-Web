import { Controller } from '@hotwired/stimulus';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['categoryPicker']
    static values = { url: String }

    connect() {
    }

    /**
     * @param {Event} event
     */
    async suggestCategory(event) {
        const { target } = event;
        const { value } = target;
        if (!value) return;
        const category = await this.#retrieveCategory(value);
        this.categoryPickerTarget.value = category;
    }


    /**
     * @param {string} content
     * @returns {Promise<'Doctor'|'Pharmacy'|'Medication'|'Other'>} category
     */
    async #retrieveCategory(content) {
        const formData = new FormData();
        formData.append('content', content);

        const response = await fetch(this.urlValue, { method: 'POST', body: formData });
        const json = await response.json();
        return json.category;
    }
}