jQuery(document).ready(function($){
    $('#member_post_tracker_upload_button').click(function(e) {
        e.preventDefault();
        var image = wp.media({ 
            title: 'Upload Image for Button',
            multiple: false
        }).open().on('select', function(e){
            var uploaded_image = image.state().get('selection').first();
            var image_url = uploaded_image.toJSON().url;
            $('#member_post_tracker_button_image').val(image_url);
        });
    });
});
