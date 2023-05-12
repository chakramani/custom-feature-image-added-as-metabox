<?php

function enqueue_admin_script()
{
	wp_enqueue_script('cpm_custom_script', get_template_directory_uri() . '/cpm_admin_main.js');
}
add_action('admin_enqueue_scripts', 'enqueue_admin_script');


add_action('add_meta_boxes', 'feature_image_add_metabox');
function feature_image_add_metabox()
{
	add_meta_box('featureimagediv', __('Feature Image', 'text-domain'), 'feature_image_metabox', 'ht_kb', 'side', 'low');
}

function feature_image_metabox($post)
{
	global $content_width, $_wp_additional_image_sizes;

	$image_id = get_post_meta($post->ID, '_feature_image_id', true);

	$old_content_width = $content_width;
	$content_width = 254;

	if ($image_id && get_post($image_id)) {

		if (!isset($_wp_additional_image_sizes['post-thumbnail'])) {
			$thumbnail_html = wp_get_attachment_image($image_id, array($content_width, $content_width));
		} else {
			$thumbnail_html = wp_get_attachment_image($image_id, 'post-thumbnail');
		}

		if (!empty($thumbnail_html)) {
			$content = $thumbnail_html;
			$content .= '<p class="hide-if-no-js"><a href="javascript:;" id="remove_feature_image_button" >' . esc_html__('Remove feature image', 'text-domain') . '</a></p>';
			$content .= '<input type="hidden" id="upload_feature_image" name="_feature_cover_image" value="' . esc_attr($image_id) . '" />';
		}

		$content_width = $old_content_width;
	} else {

		$content = '<img src="" style="width:' . esc_attr($content_width) . 'px;height:auto;border:0;display:none;" />';
		$content .= '<p class="hide-if-no-js"><a title="' . esc_attr__('Set feature image', 'text-domain') . '" href="javascript:;" id="upload_feature_image_button" id="set-feature-image" data-uploader_title="' . esc_attr__('Choose an image', 'text-domain') . '" data-uploader_button_text="' . esc_attr__('Set feature image', 'text-domain') . '">' . esc_html__('Set feature image', 'text-domain') . '</a></p>';
		$content .= '<input type="hidden" id="upload_feature_image" name="_feature_cover_image" value="" />';
	}

	echo $content;
}

add_action('save_post', 'feature_image_save', 10, 1);
function feature_image_save($post_id)
{
	if (isset($_POST['_feature_cover_image'])) {
		$image_id = (int) $_POST['_feature_cover_image'];
		update_post_meta($post_id, '_feature_image_id', $image_id);
	}
}
