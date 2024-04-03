import { Controller } from "@hotwired/stimulus"
import { useTargetMutation, useThrottle, useDebounce } from "stimulus-use"
import { getComponent } from "@symfony/ux-live-component"

export default class extends Controller {
  static targets = ["writingArea", "suggestion", "example"]

  static throttles = [
    {
      name: "suggest",
      wait: 2500,
    },
    {
      name: "suggest",
      wait: 1000,
    },
  ]

  static debounces = [
    {
      name: "autoSuggest",
      wait: 5000,
    },
    {
      name: "requestSuggestion",
      wait: 1000,
    },
  ]

  async initialize() {
    this.component = await getComponent(this.element)
    this.component.on("render:finished", (component) => {
      if (this.component.getData("suggestion") === null) {
        this.suggestionTarget.innerText = ""
      } else {
        this.suggestionTarget.innerText =
          this.writingAreaTarget.value + this.component.getData("suggestion")
      }
    })
  }

  connect() {
    useTargetMutation(this)
    useThrottle(this)
    useDebounce(this)
  }

  /**
   * Clears the suggestion when the text is modified
   * @param {Event} event
   */
  dismissSuggestion(event) {
    if (this.component.getData("suggestion") === null) {
      return
    }
    this.component.set("suggestion", null, false)
    this.suggestionTarget.innerText = ""
  }

  /**
   * @param {Event} event
   */
  async requestSuggestion(event) {
    await this.suggest(event)
  }

  /**
   * @param {Event} event
   */
  async autoSuggest(event) {
    await this.suggest(event)
  }

  /**
   * @param {Event} event
   */
  async suggest(event) {
    /** @type {string} */
    const diagnosis = event.target.value
    if (diagnosis.trim().length === 0) {
      return
    }
    if (document.activeElement !== event.target) {
      return
    }
    await this.component.action("genCompletion", { diagnosis })
  }

  /**
   * @param {Event} event
   */
  acceptSuggestion(event) {
    if (this.component.getData("suggestion") === null) {
      return
    }
    this.writingAreaTarget.value += this.component.getData("suggestion")
    this.component.set("suggestion", null, false)
    this.suggestionTarget.innerText = ""
  }
}
