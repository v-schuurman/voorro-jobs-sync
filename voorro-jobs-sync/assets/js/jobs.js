( function( $ ) {
        
    $(document).ready(function(){
        $('.job-item').on('click', function() {
            var selectedId = $(this).data('id'); // Get the data-id of the clicked element

            // Hide all single_job_desc elements
            $('.single_job_desc').hide();

            // Show the single_job_desc with the matching data-id
            $('.single_job_desc[data-id="' + selectedId + '"]').show();

            // Scroll to the height of job_output on mobile
            if ($(window).width() < 768) {
                $('html, body').animate({ scrollTop: $('.job_output').offset().top }, 'slow');
            }
        });

        // Show the first single_job_desc element on page load
        $('.single_job_desc:first').show();
        
        $(document).on('click', '#select_job', function() {
            let current_id = $(this).attr('data-id');
            $('select[name="job"]').val(current_id).change();
            
            // Scroll to the apply_form element with an offset of 100px above
            $('html, body').animate({ scrollTop: $('.apply_form').offset().top - 100 }, 'slow');
        });

        $('.apply_form_def').on('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            var jobText = $('select[name="job"] option:selected').text();
            formData.set('job', jobText);
            for (var pair of formData.entries()) {
                console.log(pair[0] + ':', pair[1]);
            }
            
            
            $.ajax({
                url: 'https://voorro.nl/wp-json/voorro-jobs/v1/create-post',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response && response.message) {
                        alert(response.message); // Show success message
                    } else {
                        alert('Error: Something went wrong.'); // Show error message if response is undefined
                    }
                },
                error: function(jqXHR) {
                    var errorMessage = jqXHR.responseJSON && jqXHR.responseJSON.message ? jqXHR.responseJSON.message : 'An error occurred. Please try again.';
                    alert(errorMessage);
                }
            });
            
        });


    });

} )( jQuery );  