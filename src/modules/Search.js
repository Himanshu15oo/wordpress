import $ from "jquery";

class Search {
  // 1.Describe or initiate out object
  constructor() {
    this.addSearchHtml();
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
        this.typingTimer = setTimeout(() => this.getResults(), 700);
      } else {
        this.resultsDiv.html("");
        this.isSpinnerVisible = false;
      }
    }
    this.previousValue = this.searchField.val();
  }

  getResults() {
    $.when(
      // Making multiple requests
      $.getJSON(
        universityData.root_url +
          "/wp-json/wp/v2/posts?search=" +
          this.searchField.val()
      ),
      $.getJSON(
        universityData.root_url +
          "/wp-json/wp/v2/pages?search=" +
          this.searchField.val()
      )
    ) // Collecting results from reqs
      .then(
        (posts, pages) => {
          var combinedResults = posts[0].concat(pages[0]);
          this.resultsDiv.html(`
          <h2 class="search-overlay__section-title">General Information</h2>
          ${
            combinedResults.length
              ? '<ul class="link-list min-list">'
              : "<p>No General information matches the search.</p>"
          }
          
          ${combinedResults
            .map((data) => {
              return `<li><a href="${data.link}">${data.title.rendered}</a> 
              ${data.author_name ? "by " + data.author_name : ""}</li>`;
            })
            .join("")}
            
          ${combinedResults.length ? "</ul>" : ""}
          
        `);
        },
        // Error handling
        () => {
          this.resultsDiv.html("<p>Unexpected Error, please try again.</p>");
        }
      );
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
    this.searchField.val("");
    setTimeout(() => this.searchField.focus(), 301);
    this.isOverlayOpen = true;
  }

  closeOverlay() {
    this.searchOverlay.removeClass("search-overlay--active");
    $("body").removeClass("body-no-scroll");
    this.isOverlayOpen = false;
  }

  addSearchHtml() {
    $("body").append(`
        <div class="search-overlay">
          <div class="search-overlay__top">
            <div class="container">
              <i class="fa fa-search search-overlay__icon" aria-hidden="true"></i>
              <input type="text" class="search-term" placeholder="What are you looking for?" id="search-term" autocomplete="off">
              <i class="fa fa-window-close search-overlay__close" aria-hidden="true"></i>
            </div>
          </div>

          <div class="container">
            <div id="search-overlay__results">

            </div>
          </div>
        </div>
      `);
  }
}

export default Search;
