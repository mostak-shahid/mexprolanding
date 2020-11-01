<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('melodyschool_sc_audio_theme_setup')) {
	add_action( 'melodyschool_action_before_init_theme', 'melodyschool_sc_audio_theme_setup' );
	function melodyschool_sc_audio_theme_setup() {
		add_action('melodyschool_action_shortcodes_list', 		'melodyschool_sc_audio_reg_shortcodes');
		if (function_exists('melodyschool_exists_visual_composer') && melodyschool_exists_visual_composer())
			add_action('melodyschool_action_shortcodes_list_vc','melodyschool_sc_audio_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

/*
[trx_audio url="http://trex2.themerex.dnw/wp-content/uploads/2014/12/Dream-Music-Relax.mp3" image="http://trex2.themerex.dnw/wp-content/uploads/2014/10/post_audio.jpg" title="Insert Audio Title Here" author="Lily Hunter" controls="show" autoplay="off"]
*/

if (!function_exists('melodyschool_sc_audio')) {	
	function melodyschool_sc_audio($atts, $content = null) {
		if (melodyschool_in_shortcode_blogger()) return '';
		extract(melodyschool_html_decode(shortcode_atts(array(
			// Individual params
			"title" => "",
			"author" => "",
			"image" => "",
			"mp3" => '',
			"wav" => '',
			"src" => '',
			"url" => '',
			"align" => '',
			"controls" => "",
			"autoplay" => "",
			"frame" => "on",
			// Common params
			"id" => "",
			"class" => "",
			"css" => "",
			"animation" => "",
			"width" => '',
			"height" => '',
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => ""
		), $atts)));
		if ($src=='' && $url=='' && isset($atts[0])) {
			$src = $atts[0];
		}
		if ($src=='') {
			if ($url) $src = $url;
			else if ($mp3) $src = $mp3;
			else if ($wav) $src = $wav;
		}
		if ($image > 0) {
			$attach = wp_get_attachment_image_src( $image, 'full' );
			if (isset($attach[0]) && $attach[0]!='')
				$image = $attach[0];
		}
		$class .= ($class ? ' ' : '') . melodyschool_get_css_position_as_classes($top, $right, $bottom, $left);
		$data = ($title != ''  ? ' data-title="'.esc_attr($title).'"'   : '')
				. ($author != '' ? ' data-author="'.esc_attr($author).'"' : '')
				. ($image != ''  ? ' data-image="'.esc_url($image).'"'   : '')
				. ($align && $align!='none' ? ' data-align="'.esc_attr($align).'"' : '')
				. (!melodyschool_param_is_off($animation) ? ' data-animation="'.esc_attr(melodyschool_get_animation_classes($animation)).'"' : '');
		$audio = '<audio'
			. ($id ? ' id="'.esc_attr($id).'"' : '')
			. ' class="sc_audio' . (!empty($class) ? ' '.esc_attr($class) : '') . '"'
			. ' src="'.esc_url($src).'"'
			. (melodyschool_param_is_on($controls) ? ' controls="controls"' : '')
			. (melodyschool_param_is_on($autoplay) && is_single() ? ' autoplay="autoplay"' : '')
			. ' width="'.esc_attr($width).'" height="'.esc_attr($height).'"'
			. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
			. ($data)
			. '></audio>';
		if ( melodyschool_get_custom_option('substitute_audio')=='no') {
			if (melodyschool_param_is_on($frame)) {
				$audio = melodyschool_get_audio_frame($audio, $image, $s);
			}
		} else {
			if ((isset($_GET['vc_editable']) && $_GET['vc_editable']=='true') && (isset($_POST['action']) && $_POST['action']=='vc_load_shortcode')) {
				$audio = melodyschool_substitute_audio($audio, false);
			}
		}
		if (melodyschool_get_theme_option('use_mediaelement')=='yes')
			wp_enqueue_script('wp-mediaelement');
		return apply_filters('melodyschool_shortcode_output', $audio, 'trx_audio', $atts, $content);
	}
	add_shortcode("trx_audio", "melodyschool_sc_audio");
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'melodyschool_sc_audio_reg_shortcodes' ) ) {
	//add_action('melodyschool_action_shortcodes_list', 'melodyschool_sc_audio_reg_shortcodes');
	function melodyschool_sc_audio_reg_shortcodes() {
	
		melodyschool_sc_map("trx_audio", array(
			"title" => esc_html__("Audio", 'trx_utils'),
			"desc" => wp_kses_data( __("Insert audio player", 'trx_utils') ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"url" => array(
					"title" => esc_html__("URL for audio file", 'trx_utils'),
					"desc" => wp_kses_data( __("URL for audio file", 'trx_utils') ),
					"readonly" => false,
					"value" => "",
					"type" => "media",
					"before" => array(
						'title' => esc_html__('Choose audio', 'trx_utils'),
						'action' => 'media_upload',
						'type' => 'audio',
						'multiple' => false,
						'linked_field' => '',
						'captions' => array( 	
							'choose' => esc_html__('Choose audio file', 'trx_utils'),
							'update' => esc_html__('Select audio file', 'trx_utils')
						)
					),
					"after" => array(
						'icon' => 'icon-cancel',
						'action' => 'media_reset'
					)
				),
				"image" => array(
					"title" => esc_html__("Cover image", 'trx_utils'),
					"desc" => wp_kses_data( __("Select or upload image or write URL from other site for audio cover", 'trx_utils') ),
					"readonly" => false,
					"value" => "",
					"type" => "media"
				),
				"title" => array(
					"title" => esc_html__("Title", 'trx_utils'),
					"desc" => wp_kses_data( __("Title of the audio file", 'trx_utils') ),
					"divider" => true,
					"value" => "",
					"type" => "text"
				),
				"author" => array(
					"title" => esc_html__("Author", 'trx_utils'),
					"desc" => wp_kses_data( __("Author of the audio file", 'trx_utils') ),
					"value" => "",
					"type" => "text"
				),
				"controls" => array(
					"title" => esc_html__("Show controls", 'trx_utils'),
					"desc" => wp_kses_data( __("Show controls in audio player", 'trx_utils') ),
					"divider" => true,
					"size" => "medium",
					"value" => "show",
					"type" => "switch",
					"options" => melodyschool_get_sc_param('show_hide')
				),
				"autoplay" => array(
					"title" => esc_html__("Autoplay audio", 'trx_utils'),
					"desc" => wp_kses_data( __("Autoplay audio on page load", 'trx_utils') ),
					"value" => "off",
					"type" => "switch",
					"options" => melodyschool_get_sc_param('on_off')
				),
				"align" => array(
					"title" => esc_html__("Align", 'trx_utils'),
					"desc" => wp_kses_data( __("Select block alignment", 'trx_utils') ),
					"value" => "none",
					"type" => "checklist",
					"dir" => "horizontal",
					"options" => melodyschool_get_sc_param('align')
				),
				"width" => melodyschool_shortcodes_width(),
				"height" => melodyschool_shortcodes_height(),
				"top" => melodyschool_get_sc_param('top'),
				"bottom" => melodyschool_get_sc_param('bottom'),
				"left" => melodyschool_get_sc_param('left'),
				"right" => melodyschool_get_sc_param('right'),
				"id" => melodyschool_get_sc_param('id'),
				"class" => melodyschool_get_sc_param('class'),
				"animation" => melodyschool_get_sc_param('animation'),
				"css" => melodyschool_get_sc_param('css')
			)
		));
	}
}


/* Register shortcode in the VC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'melodyschool_sc_audio_reg_shortcodes_vc' ) ) {
	//add_action('melodyschool_action_shortcodes_list_vc', 'melodyschool_sc_audio_reg_shortcodes_vc');
	function melodyschool_sc_audio_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_audio",
			"name" => esc_html__("Audio", 'trx_utils'),
			"description" => wp_kses_data( __("Insert audio player", 'trx_utils') ),
			"category" => esc_html__('Content', 'trx_utils'),
			'icon' => 'icon_trx_audio',
			"class" => "trx_sc_single trx_sc_audio",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "url",
					"heading" => esc_html__("URL for audio file", 'trx_utils'),
					"description" => wp_kses_data( __("Put here URL for audio file", 'trx_utils') ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "image",
					"heading" => esc_html__("Cover image", 'trx_utils'),
					"description" => wp_kses_data( __("Select or upload image or write URL from other site for audio cover", 'trx_utils') ),
					"class" => "",
					"value" => "",
					"type" => "attach_image"
				),
				array(
					"param_name" => "title",
					"heading" => esc_html__("Title", 'trx_utils'),
					"description" => wp_kses_data( __("Title of the audio file", 'trx_utils') ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "author",
					"heading" => esc_html__("Author", 'trx_utils'),
					"description" => wp_kses_data( __("Author of the audio file", 'trx_utils') ),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "controls",
					"heading" => esc_html__("Controls", 'trx_utils'),
					"description" => wp_kses_data( __("Show/hide controls", 'trx_utils') ),
					"class" => "",
					"value" => array("Hide controls" => "hide" ),
					"type" => "checkbox"
				),
				array(
					"param_name" => "autoplay",
					"heading" => esc_html__("Autoplay", 'trx_utils'),
					"description" => wp_kses_data( __("Autoplay audio on page load", 'trx_utils') ),
					"class" => "",
					"value" => array("Autoplay" => "on" ),
					"type" => "checkbox"
				),
				array(
					"param_name" => "align",
					"heading" => esc_html__("Alignment", 'trx_utils'),
					"description" => wp_kses_data( __("Select block alignment", 'trx_utils') ),
					"class" => "",
					"value" => array_flip(melodyschool_get_sc_param('align')),
					"type" => "dropdown"
				),
				melodyschool_get_vc_param('id'),
				melodyschool_get_vc_param('class'),
				melodyschool_get_vc_param('animation'),
				melodyschool_get_vc_param('css'),
				melodyschool_vc_width(),
				melodyschool_vc_height(),
				melodyschool_get_vc_param('margin_top'),
				melodyschool_get_vc_param('margin_bottom'),
				melodyschool_get_vc_param('margin_left'),
				melodyschool_get_vc_param('margin_right')
			),
		) );
		
		class WPBakeryShortCode_Trx_Audio extends MELODYSCHOOL_VC_ShortCodeSingle {}
	}
}
?>