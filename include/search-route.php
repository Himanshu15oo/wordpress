<?php

add_action('rest_api_init', 'universitySearch');

function universitySearch()
{
    register_rest_route('university/v1', 'search', array(
        'method' => WP_REST_Server::READABLE,
        'callback' => 'universitySearchResults'
    ));
}

function universitySearchResults($data)
{
    $query = new WP_Query(array(
        's' => sanitize_text_field($data['term']),
        'post_type' => array('post', 'page', 'program', 'professor', 'campus', 'event'),
        'posts_per_page' => -1
    ));

    $results = array(
        'general_info' => array(),
        'professors' => array(),
        'programs' => array(),
        'events' => array(),
        'campuses' => array(),
    );

    while ($query->have_posts()) {
        $query->the_post();

        if (get_post_type() == 'post' or get_post_type() == 'page') {
            array_push($results['general_info'], array(
                'type' => get_post_type(),
                'title' => get_the_title(),
                'id' => get_the_ID(),
                'link' => get_the_permalink(),
                'author' => get_the_author()
            ));
        }

        if (get_post_type() == 'professor') {
            array_push($results['professors'], array(
                'type' => get_post_type(),
                'title' => get_the_title(),
                'id' => get_the_ID(),
                'link' => get_the_permalink()
            ));
        }
        if (get_post_type() == 'program') {
            array_push($results['programs'], array(
                'type' => get_post_type(),
                'title' => get_the_title(),
                'id' => get_the_ID(),
                'link' => get_the_permalink()
            ));
        }
        if (get_post_type() == 'event') {
            array_push($results['events'], array(
                'type' => get_post_type(),
                'title' => get_the_title(),
                'id' => get_the_ID(),
                'link' => get_the_permalink()
            ));
        }
        if (get_post_type() == 'campus') {
            array_push($results['campuses'], array(
                'type' => get_post_type(),
                'title' => get_the_title(),
                'id' => get_the_ID(),
                'link' => get_the_permalink()
            ));
        }
    }

    return $results;
}
