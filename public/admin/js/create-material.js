// add more
$(document).ready(function () {
  $("body").on("click", ".add-more", function () {
    var html = $(".after-add-more").first().clone();
    $(html)
      .find(".change")
      .html(
        "<label for=''></label> <a class='btn btn-success remove'>-  Remove</a>"
      );
    $(".after-add-more").last().after(html);
  });

  $("body").on("click", ".remove", function () {
    $(this).parents(".after-add-more").remove();
  });
});

// Add More Second
$(document).ready(function () {
  $("body").on("click", ".add-more-second", function () {
    var html = $(".after-add-more-second").first().clone();
    $(html)
      .find(".change")
      .html(
        "<label for=''></label> <br> <a class='btn btn-success mt-2 remove'>-  Remove </a>"
      );
    $(".after-add-more-second").last().after(html);
  });

  $("body").on("click", ".remove", function () {
    $(this).parents(".after-add-more-second").remove();
  });
});

// Add More Third
$(document).ready(function () {
  $("body").on("click", ".add-more-third", function () {
    var html = $(".after-add-more-third").first().clone();
    $(html)
      .find(".change")
      .html(
        "<label for=''></label> <a class='btn btn-success  remove'>-  Remove </a>"
      );
    $(".after-add-more-third").last().after(html);
  });

  $("body").on("click", ".remove", function () {
    $(this).parents(".after-add-more-third").remove();
  });
});

$(document).ready(function () {
  $("body").on("click", ".add-more-four", function () {
    var html = $(".after-add-more-four").first().clone();
    $(html)
      .find(".change")
      .html(
        "<label for=''></label> <a class='btn btn-success  remove'>-  Remove </a>"
      );
    $(".after-add-more-four").last().after(html);
  });

  $("body").on("click", ".remove", function () {
    $(this).parents(".after-add-more-four").remove();
  });
});
