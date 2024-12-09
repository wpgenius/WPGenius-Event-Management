jQuery(document).ready(function($) {
    var file_frame;

    // When the upload button is clicked
    $('.upload_company_logo_button').on('click', function(e) {
        e.preventDefault();

        // If the media frame already exists, reopen it.
        if ( file_frame ) {
            file_frame.open();
            return;
        }

        // Create a new media frame
        file_frame = wp.media({
            title: 'Select or Upload a Company Logo',
            button: {
                text: 'Use this logo',
            },
            multiple: false // Set to true to allow multiple files to be selected
        });

        // When an image is selected, run a callback.
        file_frame.on('select', function() {
            var attachment = file_frame.state().get('selection').first().toJSON();
            // Set the hidden input value to the attachment ID
            $('#company_logo').val(attachment.id);
            // Display a preview of the uploaded image
            $('.company_logo_preview').html('<img src="' + attachment.url + '" style="max-width:100%;height:auto;">');
        });

        // Open the media frame
        file_frame.open();
    });
});
