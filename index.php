<?php

/*
Plugin Name: Word Filter
Description: Filters words and replaces them with your defined words.
Version: 1.0
Author: Himanshu

*/

if (!defined('ABSPATH')) exit; // Exit if access directly


class Wordfilter
{
    function __construct()
    {
        add_action('admin_menu', array($this, 'admin_menu'));
        add_action('admin_init', array($this, 'settings'));
        if (get_option('plugin_words_to_filter')) add_filter('the_content', array($this, 'apply_filter'));
    }

    function settings()
    {
        add_settings_section('options-section', null, null, 'wordfilter-options');
        register_setting('replacement-fields-group', 'replacement_options');
        add_settings_field('replacement-text', 'Filtered Text', array($this, 'replacement_field_html'), 'wordfilter-options', 'options-section');
    }

    function replacement_field_html()
    { ?>
        <input type="text" name="replacement_options" value="<?php echo esc_attr(get_option('replacement_options', '***')) ?>">
        <p class="description">Leave blank to simply remove the filtered words.</p>
        <?php }

    function apply_filter($content)
    {
        $bad_words = explode(', ', get_option('plugin_words_to_filter'));
        $bad_words_trimmed = array_map('trim', $bad_words);
        return str_ireplace($bad_words_trimmed, esc_html(get_option('replacement_options'), '****'), $content);
    }
    function admin_menu()
    {
        $hook = add_menu_page('Word Filter', 'Word Filter', 'manage_options', 'wordfilter', array($this, 'html_handler'), 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHZpZXdCb3g9IjAgMCAyMCAyMCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZmlsbC1ydWxlPSJldmVub2RkIiBjbGlwLXJ1bGU9ImV2ZW5vZGQiIGQ9Ik0xMCAyMEMxNS41MjI5IDIwIDIwIDE1LjUyMjkgMjAgMTBDMjAgNC40NzcxNCAxNS41MjI5IDAgMTAgMEM0LjQ3NzE0IDAgMCA0LjQ3NzE0IDAgMTBDMCAxNS41MjI5IDQuNDc3MTQgMjAgMTAgMjBaTTExLjk5IDcuNDQ2NjZMMTAuMDc4MSAxLjU2MjVMOC4xNjYyNiA3LjQ0NjY2SDEuOTc5MjhMNi45ODQ2NSAxMS4wODMzTDUuMDcyNzUgMTYuOTY3NEwxMC4wNzgxIDEzLjMzMDhMMTUuMDgzNSAxNi45Njc0TDEzLjE3MTYgMTEuMDgzM0wxOC4xNzcgNy40NDY2NkgxMS45OVoiIGZpbGw9IiNGRkRGOEQiLz4KPC9zdmc+Cg==', 100);
        add_submenu_page('wordfilter', 'Word Filter', 'All Filters', 'manage_options', 'wordfilter', array($this, 'html_handler'));
        add_submenu_page('wordfilter', 'Word Filter Options', 'Options', 'manage_options', 'wordfilter-options', array($this, 'options_html'));
        add_action("load-{$hook}", array($this, 'plugin_assets'));
    }

    function plugin_assets()
    {
        wp_enqueue_style('filter_admin_css', plugin_dir_url(__FILE__) . 'styles.css');
    }

    function form_handler()
    {
        if (
            isset($_POST['nonce']) and
            wp_verify_nonce($_POST['nonce'], 'save_filter_words') and
            current_user_can('manage_options')
        ) {
            update_option('plugin_words_to_filter', sanitize_text_field($_POST['plugin_words_to_filter'])); ?>
            <div class="updated">
                <p>Your filtered words were saved.</p>
            </div>
        <?php
        } else { ?>
            <div class="error">
                <p>Sorry, you do not have permission to perform this action.</p>
            </div>
        <?php }
    }

    function html_handler()
    { ?>
        <div class="wrap">
            <h1>Word filter</h1>
            <?php if (isset($_POST['submitted']) and $_POST['submitted'] == "true") $this->form_handler(); ?>
            <form action="" method="post">
                <input type="hidden" name="submitted" value="true">
                <?php wp_nonce_field('save_filter_words', 'nonce') ?>
                <label for="plugin_words_to_filter">
                    <p>Enter a <strong>comma separtaed</strong> list of words to filter from your site's contents.</p>
                    <div class="word-filter__flex-container">
                        <textarea name="plugin_words_to_filter" id="" placeholder="bad, mean, awful, horrible"><?php echo esc_textarea(get_option('plugin_words_to_filter', '')); ?></textarea>
                    </div>
                    <input type="submit" name="submit" id="submit" class="button button-primary" value="Save changes">
                </label>
            </form>
        </div>
    <?php }

    function options_html()
    { ?>
        <div class="wrap">
            <h1>Word Filter Options</h1>
            <form action="options.php" method="post">
                <?php
                settings_errors();
                settings_fields('replacement-fields-group');
                do_settings_sections('wordfilter-options');
                submit_button();
                ?>
            </form>
        </div>
<?php }
}

$wordfilter = new Wordfilter();
