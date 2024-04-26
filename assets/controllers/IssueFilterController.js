// File: assets/controllers/IssueFilterController.js

export default class IssueFilterController {
    constructor() {
        this.checkboxes = document.querySelectorAll('#category-filter input');
        this.cards = document.querySelectorAll('.group');
        this.init();
    }

    init() {
        this.checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', this.filterCards.bind(this));
        });
    }

    filterCards() {
        const checkedCategories = Array.from(this.checkboxes)
            .filter(input => input.checked)
            .map(input => input.name);

        console.log('Checked categories:', checkedCategories);

        this.cards.forEach(card => {
            const cardCategory = card.getAttribute('data-category');
            console.log('Card category:', cardCategory);

            if (checkedCategories.includes(cardCategory)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }
}