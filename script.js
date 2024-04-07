jQuery(document).ready(function($) {
    $(document).on('click', '.mark-as-read-button', function() {
        var post_id = $(this).data('post-id');
        $.ajax({
            type: 'POST',
            url: ajax_object.ajaxurl,
            data: {
                action: 'mark_post_as_read',
                post_id: post_id
            },
            success: function(response) {
                console.log('AJAX Success:', response);
                if (response === 'success') {
                    $('.mark-as-read-button[data-post-id="' + post_id + '"]').text('Read');
                }
            }
        });
    });
});
