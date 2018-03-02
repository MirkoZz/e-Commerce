$(document).ready(function() {
    $(window).scrollTop(localStorage["posStorage"]);

    $(this).click(function(e) {
        localStorage["posStorage"] = $(window).scrollTop();
    });
});