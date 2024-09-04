<?php
// Register Job Listings Custom Post Type
function create_job_post_type() {
    $labels = array(
        'name'               => __( 'Vacatures', 'voorro-jobs' ),
        'singular_name'      => __( 'Vacature', 'voorro-jobs' ),
        'menu_name'          => __( 'Vacatures', 'voorro-jobs' ),
        'name_admin_bar'     => __( 'Vacature', 'voorro-jobs' ),
        'add_new'            => __( 'Nieuwe toevoegen', 'voorro-jobs' ),
        'add_new_item'       => __( 'Nieuwe vacature toevoegen', 'voorro-jobs' ),
        'new_item'           => __( 'Nieuwe vacature', 'voorro-jobs' ),
        'edit_item'          => __( 'Vacature bewerken', 'voorro-jobs' ),
        'view_item'          => __( 'Vacature bekijken', 'voorro-jobs' ),
        'all_items'          => __( 'Alle vacatures', 'voorro-jobs' ),
        'search_items'       => __( 'Vacatures zoeken', 'voorro-jobs' ),
        'parent_item_colon'  => __( 'Hoofd vacatures:', 'voorro-jobs' ),
        'not_found'          => __( 'Geen vacatures gevonden.', 'voorro-jobs' ),
        'not_found_in_trash' => __( 'Geen vacatures gevonden in prullenbak.', 'voorro-jobs' )
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'vacatures' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array( 'title', 'editor' ),
        'show_in_rest'       => true, // Enables the REST API for this post type
    );

    register_post_type( 'job', $args );
}
add_action( 'init', 'create_job_post_type' );


function create_applications_post_type() {
    $labels = array(
        'name'               => __( 'Sollicitaties', 'voorro-jobs' ),
        'singular_name'      => __( 'Sollicitatie', 'voorro-jobs' ),
        'menu_name'          => __( 'Sollicitaties', 'voorro-jobs' ),
        'name_admin_bar'     => __( 'Sollicitatie', 'voorro-jobs' ),
        'add_new'            => __( 'Nieuwe sollicitatie toevoegen', 'voorro-jobs' ),
        'add_new_item'       => __( 'Nieuwe sollicitatie toevoegen', 'voorro-jobs' ),
        'new_item'           => __( 'Nieuwe sollicitatie', 'voorro-jobs' ),
        'edit_item'          => __( 'Sollicitatie bewerken', 'voorro-jobs' ),
        'view_item'          => __( 'Sollicitatie bekijken', 'voorro-jobs' ),
        'all_items'          => __( 'Alle sollicitaties', 'voorro-jobs' ),
        'search_items'       => __( 'Sollicitaties zoeken', 'voorro-jobs' ),
        'parent_item_colon'  => __( 'Hoofd sollicitaties:', 'voorro-jobs' ),
        'not_found'          => __( 'Geen sollicitaties gevonden.', 'voorro-jobs' ),
        'not_found_in_trash' => __( 'Geen sollicitaties gevonden in prullenbak.', 'voorro-jobs' )
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => 'edit.php?post_type=job', // Shows under 'Vacatures' menu
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'sollicitaties' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array( 'title' ),
        'show_in_rest'       => true, // Enables the REST API for this post type
    );

    register_post_type( 'application', $args );
}
add_action( 'init', 'create_applications_post_type' );

