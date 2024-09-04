<?php
/*
Plugin Name: Voorro Jobs Sync
Plugin URI: https://voorro.nl
Description: Gets the jobs from the Voorro API and displays them on the website
Version: 0.1
Author: Vincent Schuurman
Author URI: https://zelfbouwcontainer.nl
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
Text Domain: voorro-jobs-sync
Domain Path: /languages
*/


//enqueue scripts and styles
function custom_scripts_voorro_jobs() {
    wp_enqueue_script( 'jobs', plugins_url('assets/js/jobs.js', __FILE__), array('jquery'), '1.0.0', true );
    wp_enqueue_style( 'jobs_style', plugins_url( 'assets/css/style.css', __FILE__ ) );
}

add_action('wp_enqueue_scripts', 'custom_scripts_voorro_jobs');

// Function to get data from the Voorro API
function get_jobs_data() {
    $response = wp_remote_get('http://voorro.nl/wp-json/voorro-jobs/v1/jobs/');
    
    if (is_wp_error($response)) {
        return 'Unable to retrieve jobs at the moment.';
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body);

    if (is_array($data)) {
        usort($data, function($a, $b) {
            return strcmp($a->title, $b->title);
        });
    }

    return $data;
}


// Shortcode function to display jobs data
function display_jobs_shortcode() {
    $jobs = get_jobs_data();

    $output = ''; 
    // Check if data is retrieved successfully and is not empty
    if (empty($jobs) || !is_array($jobs)) {
        return 'No jobs found.';
    }

    $output .= '<div class="jobs-list-container">'; // Open the jobs-list div
        $output .= '<div class="job-scroll-list">'; 
        // Loop through the jobs and build the list items
        foreach ($jobs as $job) {
            $id = isset($job->id) ? esc_html($job->id) : '';
            // Check if the necessary properties are set to avoid undefined errors
            $title = isset($job->title) ? esc_html($job->title) : '';
            $salary = isset($job->salary) ? esc_html($job->salary) : '';
            $job_sort = isset($job->job_sort) ? esc_html($job->job_sort) : '';
            $location = isset($job->location) ? esc_html($job->location) : '';

            $output .= '<div class="job-item" data-id="'. $id .'">'; 

                $output .= '<h1><strong>' . $title . '</strong></h1>';

                $output .= '<div class="job-info">';

                // Only display the salary section if $salary is not empty
                if (!empty($salary)) {
                    $output .= '<div class="section">';
                    $output .= '<p><strong>Salaris</strong></p>';
                    $output .= '<div class="tag">' . $salary . '</div>';
                    $output .= '</div>';
                }

                // Only display the job sort section if $job_sort is not empty
                if (!empty($job_sort)) {
                    $output .= '<div class="section">';
                    $output .= '<p><strong>Dienstverband</strong></p>';
                    $output .= '<div class="tag">' . $job_sort . '</div>';
                    $output .= '</div>';
                }

                // Only display the location section if $location is not empty
                if (!empty($location)) {
                    $output .= '<div class="section">';
                    $output .= '<p><strong>Locatie</strong></p>';
                    $output .= '<div class="tag">' . $location . '</div>';
                    $output .= '</div>';
                }

                $output .= '</div>'; // Close the job-info div

                
            $output .= '</div>';
        }
        $output .= '</div>';
        $output .= '<div class="job_output">';

        foreach ($jobs as $job) {
            $id = isset($job->id) ? esc_html($job->id) : '';
            $title = isset($job->title) ? esc_html($job->title) : '';
            $content = isset($job->content) ? wp_kses_post($job->content) : '';

            $output .= '<div class="single_job_desc" data-id="'. $id .'">';
                $output .= '<div class="job-header">'; 
                    $output .= '<h2><strong>'. $title. '</strong></h2>';
                    $output .= '<button class="apply_button" id="select_job" data-id="'. $id .'">Soliciteer direct</button><br>';
                    $output .= '<div class="job-line"></div>';
                $output .= '</div>';
                $output .= '<div class="job-content">' . $content. '</div>';       
            $output .= '</div>';
        }

        $output .= '</div>';
    $output .= '</div>'; // Close the jobs-list div


    //apply form
    $output .= '<div class="apply_form">';
        $output .= '<h2>Solliciteer direct</h2>';
        $output .= '<form class="apply_form_def">';
            $output .= '<input type="text" name="name" placeholder="Naam">';
            $output .= '<input type="email" name="email" placeholder="Email">';
            $output .= '<input type="text" name="phone" placeholder="Telefoon">';
            //select
            $output .= '<select name="job" id="job">';
                $output .= '<option value="job">Selecteer een vacature</option>';
                foreach ($jobs as $job) {
                    $id = isset($job->id) ? esc_html($job->id) : '';
                    $title = isset($job->title) ? esc_html($job->title) : '';
                    $output .= '<option value="'. $id .'">'. $title .'</option>';
                }
            $output .= '</select>';
            
            $output .= 'Upload jouw cv en/of motivatiebrief';
            $output .= '<input type="file" name="cv" placeholder="CV">';
            $output .= '<button type="submit" class="apply_button">Solliciteer</button>';
        $output .= '</form>';
    $output .= '</div>';

    return $output; // Correctly returning the output
}

// Register the shortcode
add_shortcode('display_jobs', 'display_jobs_shortcode');