<?php
function add_custom_cors_headers() {
    header("Access-Control-Allow-Origin: *");  // Replace * with your specific domain for better security
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");
}
add_action('rest_api_init', 'add_custom_cors_headers', 15);



function register_job_rest_route() {
    register_rest_route('voorro-jobs/v1', '/jobs/', array(
        'methods'  => 'GET',
        'callback' => 'get_jobs_data',
        'permission_callback' => '__return_true', // Allows public access; change this if needed
    ));
}
add_action('rest_api_init', 'register_job_rest_route');


function get_jobs_data($request) {
    // Define the query arguments
    $args = array(
        'post_type'      => 'job',
        'post_status'    => 'publish',
        'numberposts'    => -1 // Get all jobs
    );

    // Fetch jobs
    $jobs = get_posts($args);

    // If no jobs found, return an error
    if (empty($jobs)) {
        return new WP_Error('no_jobs', 'No jobs found', array('status' => 404));
    }

    // Prepare the response
    $response = array();
    foreach ($jobs as $job) {
        $salary = get_field('salary', $job->ID);
        $job_sort = get_field('job_sort', $job->ID);
        $location = get_field('location', $job->ID);
        $salary = empty($salary) ? '' : $salary;
        $job_sort = empty($job_sort) ? '' : $job_sort;
        $location = empty($location) ? '' : $location;
        $response[] = array(
            'id'      => $job->ID,
            'title'   => $job->post_title,
            'content' => $job->post_content,
            'salary'  => $salary,
            'job_sort' => $job_sort,
            'location' => $location
        );
    }

    // Return the response
    return new WP_REST_Response($response, 200);
}

// Register REST API endpoint
add_action('rest_api_init', function () {
    register_rest_route('voorro-jobs/v1', '/create-post', [
        'methods' => 'POST',
        'callback' => 'handle_create_post_request',
        'permission_callback' => '__return_true', // You can change this to check for specific permissions or authentication
    ]);
});


function handle_create_post_request(WP_REST_Request $request) {
    // If using JSON, use get_json_params(), but since we are sending FormData, use $_POST and $_FILES.
    // You might have mixed methods causing issues. Let's directly access $_POST and $_FILES:

    error_log('Received data: ' . print_r($_POST, true));  // Debugging: Log received form data
    error_log('Received files: ' . print_r($_FILES, true));  // Debugging: Log received files data

    // Handle file upload
    if (!empty($_FILES['cv']['name'])) {  // Check if file is actually uploaded
        $cv = $_FILES['cv'];

        if ($cv['error'] !== UPLOAD_ERR_OK) {
            return new WP_REST_Response(['message' => 'CV geupload met een error: ' . $cv['error']], 400);
        }

        // Proceed with handling the uploaded file
        $upload = wp_handle_upload($cv, ['test_form' => false]);

        if (isset($upload['error']) && $upload['error'] != 0) {
            return new WP_REST_Response(['message' => 'CV upload error:  ' . $upload['error']], 400);
        } else {
            $uploaded_file_url = $upload['url'];  // URL of the uploaded file
        }
    } else {
        return new WP_REST_Response(['message' => 'Geen CV geupload'], 400);
    }

    // Accessing form data directly from $_POST
    $name = sanitize_text_field($_POST['name'] ?? '');
    $email = sanitize_email($_POST['email'] ?? '');
    $phone = sanitize_text_field($_POST['phone'] ?? '');
    $job = sanitize_text_field($_POST['job'] ?? '');
    $cv_url = esc_url_raw($uploaded_file_url ?? '');

    // Check required fields
    if (empty($name) || empty($email) || empty($job) || empty($cv_url)) {
        return new WP_REST_Response(['message' => 'Vul alle velden in'], 400);
    }

    // Create the post
    $post_data = [
        'post_title'   => $name, // Use the name as the post title
        'post_content' => '', // Add more details if you want
        'post_status'  => 'publish', // Change to 'draft' if you don't want it to be published immediately
        'post_type'    => 'application', // Replace with your custom post type
        'meta_input'   => [
            'email' => $email,
            'phone' => $phone,
            'job'   => $job,
            'cv_url' => $cv_url, // Store the URL of the uploaded file as post meta
        ],
    ];

    $post_id = wp_insert_post($post_data);

    if (is_wp_error($post_id)) {
        return new WP_REST_Response(['message' => 'Error creating post'], 500);
    }

    // Email parameters
    $to = 'hr@voorro.nl'; // Replace with the email address where you want to send the form data
    $subject = 'Nieuwe sollicitatie ontvangen';
    $message = "Vacature: $job\n";
    $message .= "Naam: $name\n";
    $message .= "Emailadres: $email\n";
    $message .= "Telefoonnumer: $phone\n";
    $message .= "CV URL: $cv_url\n"; // Include the URL of the uploaded CV

    $headers = [
        'From: Voorro Website <info@voorro.nl>',  // Replace with your domain email
        'Reply-To: ' . $email,
        'Content-Type: text/plain; charset=UTF-8'
    ];

    // Send the email
    $mail_sent = wp_mail($to, $subject, $message, $headers);

    if (!$mail_sent) {
        return new WP_REST_Response(['message' => 'We hebben je sollicitatie ontvagen, maar er is een probleem met het versturen van de e-mai, gelieve contact op te nemen met ons'], 500);
    }

    return new WP_REST_Response(['message' => 'Bedankt voor de sollicitatie, wij nemen zo snel mogelijk contact met jou op.', 'post_id' => $post_id], 200);
}
