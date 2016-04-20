<?php
/* Plugin Name: Jell Image Upload Auto Post
  Plugin URI: http://wordpress.org/extend/plugins/jell-image-upload-auto-post/
  Description: This plugin will provide you the facility to create automated post when you will upload an image to your wordpress media gallery. Each time after uploading one media file upload one post will be created with attached this uploaded image automatically
  Version: 0.1
  Author: Mudassar Ali <sahil_bwp@yahoo.com>
  Author URI: http://www.techjell.com/
  @copyright  2015 Techjell
  License: GPLv2
 */

/* * **** SECURITE / SECURITY ***** */
if (!function_exists('add_action')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit('No script kiddies please!');
}

// Add settings link on plugin page
function jell_iuap_settings_link($links) {
    $settings_link = '<a href="options-general.php?page=jell_image_upload_auto_post">Settings</a>';
    array_unshift($links, $settings_link);
    return $links;
}

$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'jell_iuap_settings_link');
/**
 * admin menu hook 
 */
add_action('admin_menu', 'jell_iuap_add_admin_menu');

/**
 * admin init hook
 */
add_action('admin_init', 'jell_iuap_settings_init');

/**
 * admin menu hook implementation
 */
function jell_iuap_add_admin_menu() {

    add_options_page(
            'Jell Image Upload Auto Post', 'Jell Image Upload Auto Post', 'manage_options', 'jell_image_upload_auto_post', 'jell_image_upload_auto_post_options_page'
    );
}

/**
 * admin init hook implementation
 */
function jell_iuap_settings_init() {

    register_setting('pluginPage', 'jell_iuap_settings');

    add_settings_section(
            'jell_iuap_pluginPage_section', __('Automatically Created Page Settings', 'jell_text_domain'), 'jell_iuap_settings_section_callback', 'pluginPage'
    );

    add_settings_field(
            'jell_iuap_field_cat', __('Post Default Category', 'jell_text_domain'), 'jell_iuap_field_cat_render', 'pluginPage', 'jell_iuap_pluginPage_section'
    );

    add_settings_field(
            'jell_iuap_field_media', __('Post Default Media Type', 'jell_text_domain'), 'jell_iuap_field_media_render', 'pluginPage', 'jell_iuap_pluginPage_section'
    );

    add_settings_field(
            'jell_iuap_field_license', __('Image Default License ', 'jell_text_domain'), 'jell_iuap_field_license_render', 'pluginPage', 'jell_iuap_pluginPage_section'
    );

    add_settings_field(
            'jell_iuap_field_url', __('License Page Url', 'jell_text_domain'), 'jell_iuap_field_url_render', 'pluginPage', 'jell_iuap_pluginPage_section'
    );

    add_settings_field(
            'jell_iuap_field_tags', __('Post Default Tags', 'jell_text_domain'), 'jell_iuap_field_tags_render', 'pluginPage', 'jell_iuap_pluginPage_section'
    );
    add_settings_field(
            'jell_iuap_field_template', __('Post Default Template', 'jell_text_domain'), 'jell_iuap_field_template_render', 'pluginPage', 'jell_iuap_pluginPage_section'
    );
}

/**
 * option page fields reneder
 */
function jell_iuap_field_cat_render() {
    $options = get_option('jell_iuap_settings');
    $categories = get_categories(array(
        'orderby' => 'name',
        'order' => 'ASC',
        'taxonomy' => 'category',
        'hierarchical' => 1,
        'hide_empty' => 0,
    ));
    ?>
    <select name='jell_iuap_settings[jell_iuap_field_cat]'>
        <option value='0' <?php selected($options['jell_iuap_field_cat'], 0); ?>> No Category</option>
        <?php foreach ($categories as $category) { ?>
            <option value='<?php echo esc_attr($category->term_id); ?>' <?php selected($options['jell_iuap_field_cat'], esc_attr($category->term_id)); ?>><?php echo esc_attr($category->name); ?></option>
        <?php } ?>
    </select>

    <?php
}

/**
 * option page fields reneder
 */
function jell_iuap_field_media_render() {

    $options = get_option('jell_iuap_settings');
    ?>
    <select name='jell_iuap_settings[jell_iuap_field_media]'>
        <option value='1' <?php selected($options['jell_iuap_field_media'], 1); ?>>Photos</option>
        <option value='2' <?php selected($options['jell_iuap_field_media'], 2); ?>>Vector graphics</option>
        <option value='3' <?php selected($options['jell_iuap_field_media'], 3); ?>>Illustrations</option>
        <option value='4' <?php selected($options['jell_iuap_field_media'], 4); ?>>Videos</option>
    </select>

    <?php
}

/**
 * option page fields reneder
 */
function jell_iuap_field_license_render() {

    $options = get_option('jell_iuap_settings');
    ?>
    <select name='jell_iuap_settings[jell_iuap_field_license]'>
        <option value='1' <?php selected($options['jell_iuap_field_license'], 1); ?>>Public Domain Work</option>
        <option value='2' <?php selected($options['jell_iuap_field_license'], 2); ?>>Public Domain Dedication</option>
        <option value='3' <?php selected($options['jell_iuap_field_license'], 3); ?>>Attribution</option>
        <option value='4' <?php selected($options['jell_iuap_field_license'], 4); ?>>Copyright restrictions</option>
    </select>

    <?php
}

/**
 * option page fields reneder
 */
function jell_iuap_field_url_render() {

    $options = get_option('jell_iuap_settings');
    ?>
    <input size="60" type='text' name='jell_iuap_settings[jell_iuap_field_url]' value='<?php echo $options['jell_iuap_field_url']; ?>'>
    <?php
}

/**
 * option page fields reneder
 */
function jell_iuap_field_tags_render() {

    $options = get_option('jell_iuap_settings');
    ?>
    <input size="60" type='text' name='jell_iuap_settings[jell_iuap_field_tags]' value='<?php echo $options['jell_iuap_field_tags']; ?>'>
    <?php
}

/**
 * option page fields reneder
 */
function jell_iuap_field_template_render() {

    $options = get_option('jell_iuap_settings');
    if (empty($options['jell_iuap_field_template'])) {
        $options['jell_iuap_field_template'] = '<p><img title="{title}" alt="{title} photo" src="{src}" /></p>';
    }
    ?>
    <input size="60" type='text' name='jell_iuap_settings[jell_iuap_field_template]' value='<?php echo $options['jell_iuap_field_template']; ?>'>
    <?php
}

/**
 * option page section call back
 */
function jell_iuap_settings_section_callback() {

    echo __('Please set the defult post settings like: Post Category etc', 'jell_text_domain');
}

/**
 * option page function
 */
function jell_image_upload_auto_post_options_page() {
    ?>
    <form action='options.php' method='post'>

        <h2>Jell Image Upload Auto Post</h2>

        <?php
        settings_fields('pluginPage');
        do_settings_sections('pluginPage');
        submit_button();
        ?>
    </form>
    <h3>Available Template Tags</h3>
    <p>You can use the following tags in the "<strong>Image Template</strong>" setting field:</p>
    <p>
        <strong>{keyword}</strong> - The keyword you searched for with ImageInject.<br>
        <strong>{yoast-keyword}</strong> - Inserts the "Focus Keyword" as set in the WordPress SEO by Yoast plugin for the post.<br>	
        <strong>{title}</strong> - The title of the image on Flickr.<br>
        <strong>{description}</strong> -  The description of the image on Flickr<br>
        <strong>{author}</strong> - Flickr name or username of the author.<br>
        <strong>{link}</strong> - Link to the image page on Flickr<br>
        <strong>{src}</strong> - The image file in the specified size<br>
    </p>
    <p>The following tags are available in the "<strong>Attribution Template</strong>" field:</p>	
    <p>
        <strong>{keyword}</strong> - The keyword you searched for with ImageInject.<br>
        <strong>{author}</strong> - Flickr name or username of the author.<br>
        <strong>{link}</strong> - Link to the image page on Flickr<br>
        <strong>{cc_icon}</strong> - A small creative commons icon with a link to the license<br>
        <strong>{license_name}</strong> - The name of the creative commons license the photo uses<br>
        <strong>{license_link}</strong> - The link to the creative commons license the photo uses<br>
    </p>
    <p>The following tags are available in the "<strong>Filename Template</strong>" field:</p>	
    <p>
        <strong>{filename}</strong> - The original filename.<br>
        <strong>{keyword}</strong> - The keyword you searched for.<br>
        <strong>{timestamp}</strong> - Timestamp of when the image was uploaded to your blog.<br>
        <strong>{date}</strong> - Date of when the image was uploaded to your blog.<br>		
        <strong>{rand}</strong> - A random number.<br>
    </p>	

    <div class="clear"></div>
    <?php
}

/**
 * Adding classes and scripts
 */
foreach (glob(plugin_dir_path(__FILE__) . "inc/*.php") as $inc) {
    include_once $inc;
}
?>