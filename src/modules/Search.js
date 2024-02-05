import $ from "jquery";

class Search {
  // 1.Describe or initiate out object
  constructor() {
    this.resultsDiv = $("#search-overlay__results");
    this.openButton = $(".js-search-trigger");
    this.closeButton = $(".search-overlay__close");
    this.searchOverlay = $(".search-overlay");
    this.searchField = $("#search-term");
    this.typingTimer;
    this.events();
    this.previousValue;
    this.isOverlayOpen = false;
    this.isSpinnerVisible = false;
  }

  // 2. Events
  events() {
    this.openButton.on("click", () => this.openOverlay());
    this.closeButton.on("click", () => this.closeOverlay());
    $(document).on("keyup", (e) => this.keyPressDispatcher(e));
    this.searchField.on("keyup", () => this.typingLogic());
  }

  // 3. Methods
  typingLogic() {
    if (this.searchField.val() != this.previousValue) {
      clearTimeout(this.typingTimer);

      if (this.searchField.val()) {
        if (!this.isSpinnerVisible) {
          this.resultsDiv.html("<div class='spinner-loader'></div>");
          this.isSpinnerVisible = true;
        }
        this.typingTimer = setTimeout(() => this.getResults(), 2000);
      } else {
        this.resultsDiv.html("");
        this.isSpinnerVisible = false;
      }
    }
    this.previousValue = this.searchField.val();
  }

  getResults() {
    this.resultsDiv.html("Imagine the results...");
    this.isSpinnerVisible = false;
  }

  keyPressDispatcher(e) {
    // console.log(event.keyCode);
    if (
      e.keyCode == 83 &&
      !this.isOverlayOpen &&
      !$("input , textarea").is(":focus")
    ) {
      this.openOverlay();
    }

    if (e.keyCode == 27 && this.isOverlayOpen) {
      this.closeOverlay();
    }
  }

  openOverlay() {
    this.searchOverlay.addClass("search-overlay--active");
    $("body").addClass("body-no-scroll");
    this.isOverlayOpen = true;
  }

  closeOverlay() {
    this.searchOverlay.removeClass("search-overlay--active");
    $("body").removeClass("body-no-scroll");
    this.isOverlayOpen = false;
  }
}

export default Search;
