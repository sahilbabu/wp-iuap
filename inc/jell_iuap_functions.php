<?php

/* * **** SECURITE / SECURITY ***** */
if (!function_exists('add_action')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

/**
 * Wordpress attachment Hook 
 */
add_action('add_attachment', 'jell_auto_post_after_image_upload');

/**
 * Wordpress activation Hook 
 */
register_activation_hook(__FILE__, 'jell_flush_rule');

/**
 * Wordpress deactivation Hook 
 */
register_deactivation_hook(__FILE__, 'jell_flush_rule');

/**
 *  attachment Hook implementation
 */
function jell_auto_post_after_image_upload($attachId) {

    $attachment = get_post($attachId);
    $options = get_option('jell_iuap_settings');
    if ($attachment) {
        $image = wp_get_attachment_image_src($attachId, 'large');
        $width = $image[1];
        $height = $image[2];
        $template = $options['jell_iuap_field_template'];
        $template = str_replace('{src}', $image[0], $template);
        $template = str_replace('{title}', $attachment->post_title, $template);

        $cat = $options['jell_iuap_field_cat'];
        $tags = $options['jell_iuap_field_tags'];
        $media = $options['jell_iuap_field_media'];
        $license = $options['jell_iuap_field_license'];

        /* ---[orientation calculate]--- */
        $orientation = 'Horizontal';
        if ($height > $width) {
            $orientation = 'Vertical';
        }

        $postData = array(
            'post_title' => $attachment->post_title,
            'post_type' => 'post',
            'post_content' => $template,
            'post_category' => (isset($cat)) ? [$cat] : array('0'),
            'tags_input' => (isset($tags)) ? $tags : NULL,
            'post_status' => 'publish',
        );

        $post_id = wp_insert_post($postData);

        /* ---[ading custom fields]--- */
        update_post_meta($post_id, 'jell_image_orientation', $orientation);
        update_post_meta($post_id, 'jell_image_media_type', $media);
        update_post_meta($post_id, 'jell_image_under_law', $license);

        // update attachment metadata //
        $postinfo = array(
            'ID' => $attachId,
            'post_content' => $attachment->post_title,
            'post_excerpt' => $attachment->post_title, // caption
        );
        wp_update_post($postinfo);
        update_post_meta($attachId, '_wp_attachment_image_alt', trim($attachment->post_title));
        // attach media to post
        wp_update_post(array(
            'ID' => $attachId,
            'post_parent' => $post_id,
        ));

        set_post_thumbnail($post_id, $attachId);
    }
    return $attachId;
}

/**
 *  flush rule fuction
 */
function jell_flush_rule() {
    flush_rewrite_rules();
}

/**
 *  tags extract
 */
function jell_iuap_replace_tags_with_value($text = NULL, $replace) {
    if (!empty($text)) {
        $text = str_replace('{src}', $replace, $text);
    }
    return $text;
}

/**
 *  tags templatess
 */
function jell_iuap_tags() {
    return [
        '{keyword}',
        '{yoast-keyword}',
        '{title}',
        '{description}',
        '{author}',
        '{link}',
        '{src}',
        '{license_name}',
        '{license_link}',
        '{filename}',
    ];
}
