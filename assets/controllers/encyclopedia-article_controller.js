import { Controller } from '@hotwired/stimulus';
import {
  useDebounce,
  useThrottle,
  useClickOutside
} from 'stimulus-use';

import {
  computePosition,
  inline,
  autoUpdate,
  shift,
  flip,
  offset,
} from '@floating-ui/dom';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {

  static values = {
    apiUrl: String
  };

  static targets = [
    'writingArea',
    'tooltip',
    'modal',
    'article'
  ];

  static debounces = [
    {
      name: 'requestTooltip',
      wait: 1000,
    },
  ];

  static throttles = [
    {
      name: 'showArticle',
      wait: 2000,
    },
  ];

  cache = {};


  connect() {
    useDebounce(this);
    useThrottle(this);
    useClickOutside(this, { element: this.writingAreaTarget })
  }

  onMouseUp(event) {

    // Obtain the selected text
    const sel = this.#textAreaSelection(this.writingAreaTarget);

    // No selection means simple click

    if (!sel) {
      // hide if tooltip is not hidden
      this.#hideTooltip();
      return;
    }

    this.requestTooltip(event);
  }

  requestTooltip(event) {

    const sel = this.#textAreaSelection(this.writingAreaTarget);
    if (!sel) return;

    this.tooltipTarget.classList.remove('hidden');

    // get mouse coords
    const { clientX, clientY } = event;

    const fakeEl = {
      getBoundingClientRect() {
        return {
          width: 0,
          height: 0,
          x: clientX,
          y: clientY,
          left: clientX,
          right: clientX,
          top: clientY,
          bottom: clientY
        };
      }
    }

    computePosition(fakeEl, this.tooltipTarget, {
      placement: "right-start",
      middleware: [
        offset(5),
        flip(),
        shift(),
      ]
    }).then(({ x, y }) => {
      Object.assign(this.tooltipTarget.style, { top: `${y}px`, left: `${x}px` });
    });
  }

  clickOutside(event) {
    this.#hideTooltip();
  }

  #hideTooltip() {
    if (!this.tooltipTarget.classList.contains('hidden')) {
      this.tooltipTarget.classList.add('hidden');
    }
  }

  #showTooltip() {
    if (this.tooltipTarget.classList.contains('hidden')) {
      this.tooltipTarget.classList.remove('hidden');
    }
  }

  /**
   * @param {string} term Search term
   * @returns {Promise<Object>} html article
   */
  async fetchArticle(term) {
    const url = new URL(this.apiUrlValue);
    url.search = new URLSearchParams({ term }).toString();

    const json = await fetch(url.toString(), { method: "GET" })
      .then(res => res.json());
    return json;
  }

  async showArticle(event) {
    const sel = this.#textAreaSelection(this.writingAreaTarget);
    if (!sel) return;
    if (this.cache[sel]) {
      this.articleTarget.innerHTML = this.cache[sel];
      this.#showModal();
      return;
    }
    const result = await this.fetchArticle(sel);
    if (result.error) {
      // TODO: handle
      return;
    }

    this.cache[sel] = result.summary;

    this.articleTarget.innerHTML = result.summary;

    this.#showModal();
  }

  clearArticle(event) {
    Promise.all(
      this.modalTarget.getAnimations({ subtree: true }).map((animation) => animation.finished),
    ).then(() => {
      this.articleTarget.innerHTML = '';
    })
  }

  #showModal() {
    this.modalTarget.showModal();
    this.articleTarget.scrollIntoView();
  }

  /**
   * @param {Element} area
   * @returns {string} content
   */
  #textAreaSelection(area) {
    const { selectionStart, selectionEnd } = area;
    return area.value.substring(selectionStart, selectionEnd);
  }

}
