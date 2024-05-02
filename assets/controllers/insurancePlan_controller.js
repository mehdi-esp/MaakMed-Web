import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        const searchBar = document.getElementById('searchBar');
        const costFilter = document.getElementById('costFilter');
        const tableBody = document.querySelector('.table tbody');

        searchBar.addEventListener('input', () => filterTable('search'));
        costFilter.addEventListener('change', () => filterTable('cost'));
        ceilingFilter.addEventListener('change', () => filterTable('ceiling'));

        function filterTable(type) {
            const searchValue = searchBar.value;
            const costValue = costFilter.value;
            const ceilingValue = ceilingFilter.value;

            const url = new URL(window.location.href);
            url.searchParams.set('searchTerm', searchValue);
            url.searchParams.set('costFilter', costValue);
            url.searchParams.set('ceilingFilter', ceilingValue);

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
