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
    $.getJSON(
      universityData.root_url +
        "/wp-json/university/v1/search?term=" +
        this.searchField.val(),
      (res) => {
        this.resultsDiv.html(`
      <div class="row">
        <div class="one-third">
        <h2 class="search-overlay__section-title">General Information</h2>
        ${
          res.general_info.length
            ? '<ul class="link-list min-list">'
            : "<p>No General information matches the search.</p>"
        }
        
        ${res.general_info
          .map((data) => {
            return `<li><a href="${data.link}">${data.title}</a> 
            ${data.type == "post" ? "by " + data.author : ""}</li>`;
          })
          .join("")}
          
        ${res.general_info.length ? "</ul>" : ""}
        </div>

        <div class="one-third">
        <h2 class="search-overlay__section-title">Programs</h2>
        ${
          res.programs.length
            ? '<ul class="link-list min-list">'
            : `<p>No Program matches the search.<a href="${universityData.root_url}/programs">View all Programs</a></p>`
        }
        
        ${res.programs
          .map((data) => {
            return `<li><a href="${data.link}">${data.title}</a></li>`;
          })
          .join("")}
          
        ${res.programs.length ? "</ul>" : ""}
        <h2 class="search-overlay__section-title">Professors</h2>
        ${
          res.professors.length
            ? '<ul class="professor-cards">'
            : `<p>No Professors matches the search.</p>`
        }
        
        ${res.professors
          .map((data) => {
            return `
            <li class="professor-card__list-item">
                    <a class="professor-card" href="${data.link}">
                        <img class="professor-card__image" src="${data.photo}" alt="">
                        <span class="professor-card__name">${data.title}</span>
                    </a>
                </li>
            `;
          })
          .join("")}
          
        ${res.professors.length ? "</ul>" : ""}
        </div>

        <div class="one-third">
        <h2 class="search-overlay__section-title">Campuses</h2>
        ${
          res.campuses.length
            ? '<ul class="link-list min-list">'
            : `<p>No Campus matches the search.<a href="${universityData.root_url}/campuses">View all Campuses</a></p>`
        }
        
        ${res.campuses
          .map((data) => {
            return `<li><a href="${data.link}">${data.title}</a> 
            ${data.type == "post" ? "by " + data.author : ""}</li>`;
          })
          .join("")}
          
        ${res.campuses.length ? "</ul>" : ""}
        <h2 class="search-overlay__section-title">Event</h2>
        ${
          res.events.length
            ? ""
            : `<p>No Event matches the search.<a href="${universityData.root_url}/events">View all Events</a></p>`
        }
        
        ${res.events
          .map((data) => {
            return `
            <div class="event-summary">
                <a class="event-summary__date t-center" href="${data.link}">
                    <span class="event-summary__month">${data.month}</span>
                    <span class="event-summary__day">${data.day}</span>
                </a>
                <div class="event-summary__content">
                    <h5 class="event-summary__title headline headline--tiny"><a href="${data.link}">${data.title}</a></h5>
                    <p>${data.description} <a href="${data.link}" class="nu gray">Learn more</a></p>
                </div>
            </div>
            `;
          })
          .join("")}
          
        ${res.events.length ? "</ul>" : ""}
        </div>
      </div>
      `);
      }
    );
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
    this.searchField.val("");
    setTimeout(() => this.searchField.focus(), 301);
    this.isOverlayOpen = true;
    // Prevents default a tag behaviour
    return false;
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
