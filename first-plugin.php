<?php

/*
Plugin Name: Test Plugin
Description: This is a unique plugin
Version: 1.0
Author: Himanshu

*/

class WordCountAndTimePlugin
{
    function __construct()
    {
        add_action('admin_menu', array($this, 'admin_page'));
        add_action('admin_init', array($this, 'settings'));
        add_filter('the_content', array($this, 'apply_plugin'));
    }

    function apply_plugin($content)
    {
        if (
            is_main_query() and
            is_single()
            and
            (
                get_option('statistics_wordcount', '1') or
                get_option('statistics_charcount', '1') or
                get_option('statistics_readtime', '1')
            )
        ) {
            return $this->prepare_content($content);
        }

        return $content;
    }

    function prepare_content($content)
    {
        $plugin_text = '<h3>' . esc_html(get_option('statistics_headline', 'Post Statistics')) . '</h3><p>';

        if (get_option('statistics_wordcount', '1') or get_option('statistics_readtime', '1')) {
            $word_count = str_word_count(strip_tags($content));
        }

        if (get_option('statistics_wordcount', '1')) {
            $plugin_text .= 'This post has ' . $word_count . ' words. </br>';
        }

        if (get_option('statistics_charcount', '1')) {
            $plugin_text .= 'This post has ' . strlen(strip_tags($content)) . ' characters. </br>';
        }

        if (get_option('statistics_readtime', '1')) {
            $plugin_text .= 'This post will take about ' . round($word_count / 255) . ' minute(s). </br>';
        }
        $plugin_text .= '</p>';

        if (get_option('statistics_location', '0') == '0') {
            return $plugin_text . $content;
        }
        return $content . $plugin_text;
    }

    function settings()
    {
        add_settings_section('statistics_first_section', null, null, 'statistics');

        // Location Field
        add_settings_field('statistics_location', 'Display Location', array($this, 'location_html'), 'statistics', 'statistics_first_section');
        register_setting('statistics_group', 'statistics_location', array('sanitize_callback' => array($this, 'sanitize_location'), 'default' => '0'));

        // Headline
        add_settings_field('statistics_headline', 'Headline Text', array($this, 'headline_html'), 'statistics', 'statistics_first_section');
        register_setting('statistics_group', 'statistics_headline', array('sanitize_callback' => 'sanitize_text_field', 'default' => 'Post Statistics'));

        // checkbox - Word Count
        add_settings_field('statistics_wordcount', 'Word Count', array($this, 'checkbox_html'), 'statistics', 'statistics_first_section', array('name' => 'statistics_wordcount'));
        register_setting('statistics_group', 'statistics_wordcount', array('sanitize_callback' => 'sanitize_text_field', 'default' => '1'));

        // checkbox - Character Count
        add_settings_field('statistics_charcount', 'Char Count', array($this, 'checkbox_html'), 'statistics', 'statistics_first_section', array('name' => 'statistics_charcount'));
        register_setting('statistics_group', 'statistics_charcount', array('sanitize_callback' => 'sanitize_text_field', 'default' => '1'));

        // checkbox - Read Time
        add_settings_field('statistics_readtime', 'Read Time', array($this, 'checkbox_html'), 'statistics', 'statistics_first_section', array('name' => 'statistics_readtime'));
        register_setting('statistics_group', 'statistics_readtime', array('sanitize_callback' => 'sanitize_text_field', 'default' => '1'));
    }

    function admin_page()
    {
        add_options_page('Statistics', 'Statistics', 'manage_options', 'statistics', array($this, 'html_handler'));
    }

    function location_html()
    { ?>
        <select name="statistics_location" id="statistics_location">
            <option value="0" <?php selected(get_option('statistics_location'), '0') ?>>Beginning of Post</option>
            <option value="1" <?php selected(get_option('statistics_location'), '1') ?>>End of Post</option>
        </select>
    <?php }

    function headline_html()
    { ?>
        <input type="text" name="statistics_headline" value="<?php echo esc_attr(get_option('statistics_headline')) ?>">
    <?php }

    function checkbox_html($args)
    { ?>
        <input type="checkbox" name="<?php echo $args['name']; ?>" value="1" <?php checked(get_option($args['name']), '1') ?>>
    <?php }

    function sanitize_location($input)
    {
        if ($input != '0' and $input != '1') {
            add_settings_error('statistics_location', 'statistics_location_error', 'Display Location Needs to be either Beginning or End of Post');
            return get_option('statistics_location');
        }
        return $input;
    }


    function html_handler()
    {
    ?>
        <div class="wrap">
            <h1>Word Count Setting</h1>
            <form action="options.php" method="post">
                <?php
                settings_fields('statistics_group');
                do_settings_sections('statistics');
                submit_button();
                ?>
            </form>
        </div>
<?php
    }
}

$wordCountAndTimePlugin = new WordCountAndTimePlugin();
