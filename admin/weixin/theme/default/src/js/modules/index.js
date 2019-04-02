$(document).on("pageInit", "#page-course-index", function(e, pageId, $page) {
    $(".go-top").on("click", function() {
        $(".content").scrollTop(0);
    });

    $(".content").scroll(function() {
        var scroll = $(".content").scrollTop();
        if (scroll == 0) {
            $(".go-top").css("display", "none");
        } else {
            $(".go-top").css("display", "block");
        }
    });
});
