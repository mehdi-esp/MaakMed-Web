import { Controller } from "@hotwired/stimulus"

export default class extends Controller {

  static targets = ["picker"];

  clearFilters() {
    this.pickerTargets.forEach(el => el.tomselect?.clear(true));
  }
}
