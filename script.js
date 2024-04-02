jQuery(document).ready(function($) {
    $('.mark-as-read').on('click', function() {
        var post_id = $(this).data('post-id');
        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                action: 'mark_post_as_read',
                post_id: post_id
            },
            success: function(response) {
                if (response == 'success') {
                    $('.mark-as-read[data-post-id="' + post_id + '"]').text('Read');
                }
            }
        });
    });
});
