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
                'link' => get_the_permalink(),
                'photo' => get_the_post_thumbnail_url(0, 'professorLandscape')
            ));
        }
        if (get_post_type() == 'program') {
            // Adding related campuses
            $relatedCampuses = get_field('related_campus');
            if ($relatedCampuses) {
                foreach ($relatedCampuses as $campus) {
                    array_push($results['campuses'], array(
                        'title' => get_the_title($campus),
                        'link' => get_the_permalink($campus)
                    ));
                }
            }

            array_push($results['programs'], array(
                'type' => get_post_type(),
                'title' => get_the_title(),
                'id' => get_the_ID(),
                'link' => get_the_permalink()
            ));
        }
        if (get_post_type() == 'event') {
            $eventDate = new DateTime(get_field('event_date'));
            $description = null;
            if (has_excerpt()) {
                $description = get_the_excerpt();
            } else {
                $description = wp_trim_words(get_the_content(), 18);
            }
            array_push($results['events'], array(
                'type' => get_post_type(),
                'title' => get_the_title(),
                'id' => get_the_ID(),
                'link' => get_the_permalink(),
                'month' => $eventDate->format('M'),
                'day' => $eventDate->format('d'),
                'description' => $description
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

    if ($results['programs']) {
        $programMetaQuery = array('relation' => 'OR');

        foreach ($results['programs'] as $item) {
            array_push($programMetaQuery, array(
                'key' => 'related_programs',
                'compare' => 'LIKE',
                'value' => '"' . $item['id'] . '"'
            ));
        }

        $programRelationshipQuery = new WP_Query(array(
            'post_type' => array('professor', 'event'),
            'meta_query' => $programMetaQuery
        ));


        while ($programRelationshipQuery->have_posts()) {
            $programRelationshipQuery->the_post();

            if (get_post_type() == 'professor') {
                array_push($results['professors'], array(
                    'type' => get_post_type(),
                    'title' => get_the_title(),
                    'id' => get_the_ID(),
                    'link' => get_the_permalink(),
                    'photo' => get_the_post_thumbnail_url(0, 'professorLandscape')
                ));
            }

            if (get_post_type() == 'event') {
                $eventDate = new DateTime(get_field('event_date'));
                $description = null;
                if (has_excerpt()) {
                    $description = get_the_excerpt();
                } else {
                    $description = wp_trim_words(get_the_content(), 18);
                }
                array_push($results['events'], array(
                    'type' => get_post_type(),
                    'title' => get_the_title(),
                    'id' => get_the_ID(),
                    'link' => get_the_permalink(),
                    'month' => $eventDate->format('M'),
                    'day' => $eventDate->format('d'),
                    'description' => $description
                ));
            }
        }

        $results['professors'] = array_values(array_unique($results['professors'], SORT_REGULAR));
        $results['events'] = array_values(array_unique($results['events'], SORT_REGULAR));
    }

    return $results;
}
