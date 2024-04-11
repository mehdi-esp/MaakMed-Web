import { Controller } from '@hotwired/stimulus';

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
    connect() {
        const searchBar = document.getElementById('searchBar');
        const costFilter = document.getElementById('costFilter');
        const tableBody = document.querySelector('.table tbody');

        searchBar.addEventListener('input', () => filterTable('search'));
        costFilter.addEventListener('change', () => filterTable('cost'));

        function filterTable(type) {
            const searchValue = searchBar.value;
            const costValue = costFilter.value;

            const url = new URL(window.location.href);
            url.searchParams.set('searchTerm', searchValue);
            url.searchParams.set('costFilter', costValue);

            fetch(url)
                .then(response => response.text())
                .then(data => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(data, 'text/html');
                    const newTableBody = doc.querySelector('.table tbody');
                    tableBody.innerHTML = newTableBody.innerHTML;
                });
        }
    }
}
