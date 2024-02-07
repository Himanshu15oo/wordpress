import $ from "jquery";

class MyNotes {
  constructor() {
    this.events();
  }

  // Events
  events() {
    $("#my-notes").on("click", ".delete-note", this.deleteNote);
    $("#my-notes").on("click", ".edit-note", (e) => this.editNote(e));
    $("#my-notes").on("click", ".update-note", (e) => this.updateNote(e));
    $(".submit-note").on("click", (e) => this.createNode(e));
  }

  // Methods
  editNote(e) {
    var thisNote = $(e.target).parents("li");
    if (thisNote.data("state") == "edit") {
      this.makeNoteReadable(thisNote);
    } else {
      this.makeNoteEditable(thisNote);
    }
  }

  makeNoteEditable(thisNote) {
    thisNote
      .find(".note-title-field, .note-body-field")
      .removeAttr("readonly")
      .addClass("note-active-field");
    thisNote.find(".update-note").addClass("update-note--visible");
    thisNote
      .find(".edit-note")
      .html("<i class='fa fa-times' aria-hidden='true'></i> Cancel");
    thisNote.data("state", "edit");
  }

  makeNoteReadable(thisNote) {
    thisNote
      .find(".note-title-field, .note-body-field")
      .attr("readonly", "readonly")
      .removeClass("note-active-field");
    thisNote.find(".update-note").removeClass("update-note--visible");
    thisNote
      .find(".edit-note")
      .html("<i class='fa fa-pencil' aria-hidden='true'></i> Edit");
    thisNote.data("state", "read");
  }

  deleteNote(e) {
    var thisNote = $(e.target).parents("li");
    $.ajax({
      beforeSend: (xhr) => {
        xhr.setRequestHeader("X-WP-Nonce", universityData.nonce);
      },
      url:
        universityData.root_url + "/wp-json/wp/v2/note/" + thisNote.data("id"),
      type: "DELETE",
      success: (res) => {
        thisNote.slideUp();
        console.log("Success");
        console.log(res);
      },
      error: (res) => {
        console.log("Error");
        console.log(res);
      },
    });
  }

  updateNote(e) {
    var thisNote = $(e.target).parents("li");
    var updatedPost = {
      title: thisNote.find(".note-title-field").val(),
      content: thisNote.find(".note-body-field").val(),
    };
    $.ajax({
      beforeSend: (xhr) => {
        xhr.setRequestHeader("X-WP-Nonce", universityData.nonce);
      },
      url:
        universityData.root_url + "/wp-json/wp/v2/note/" + thisNote.data("id"),
      type: "POST",
      data: updatedPost,
      success: (res) => {
        this.makeNoteReadable(thisNote);
        console.log("Success");
        console.log(res);
      },
      error: (res) => {
        console.log("Error");
        console.log(res);
      },
    });
  }

  createNode(e) {
    var createNote = {
      title: $(".new-note-title").val(),
      content: $(".new-note-body").val(),
      status: "publish",
    };
    $.ajax({
      beforeSend: (xhr) => {
        xhr.setRequestHeader("X-WP-Nonce", universityData.nonce);
      },
      url: universityData.root_url + "/wp-json/wp/v2/note/",
      type: "POST",
      data: createNote,
      success: (res) => {
        $(".new-note-title, .new-note-body").val("");
        $(`
        <li data-id="${res.id}">
            <input readonly class="note-title-field" type="text" value="${res.title.raw}">
            <span class="edit-note"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</span>
            <span class="delete-note"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</span>
            <textarea readonly class="note-body-field">${res.content.raw}</textarea>
            <span class="update-note btn btn--blue btn--small"><i class="fa fa-arrow-right" aria-hidden="true"></i> Save</span>
        </li>
        `)
          .prependTo("#my-notes")
          .hide()
          .slideDown();
        console.log("Success");
        console.log(res);
      },
      error: (res) => {
        console.log("Error");
        console.log(res);
      },
    });
  }
}

export default MyNotes;
