(function($) {
    var wcLipscoreInit = function () {
        var reviewTabSelector = '#js-lipscore-reviews-tab';
        if ($(reviewTabSelector).length > 0) {
            // show review count
            lipscore.on('review-count-set', function(data) {
                if (data.value > 0) {
                    $('#js-lipscore-reviews-tab-count').show();
                }
            });

            // open reviews tab if reviews link clicked
            lipscore.on('review-count-link-clicked', function(data) {
                $(reviewTabSelector).parent().click();
            });
        }

        if ($('#js-lipscore-questions-tab').length > 0) {
            // show question count
            lipscore.on('question-count-set', function(data) {
                if (data.value > 0) {
                    $('#js-lipscore-questions-tab-count').show();
                }
            });
        }
    };

    if (typeof lipscore !== 'undefined') {
        wcLipscoreInit();
    } else {
        $(document).on('lipscore-created', wcLipscoreInit);
    }
})( jQuery );
