<?php

/* Theme setup section
-------------------------------------------------------------------- */
if (!function_exists('melodyschool_sc_countdown_theme_setup')) {
	add_action( 'melodyschool_action_before_init_theme', 'melodyschool_sc_countdown_theme_setup' );
	function melodyschool_sc_countdown_theme_setup() {
		add_action('melodyschool_action_shortcodes_list', 		'melodyschool_sc_countdown_reg_shortcodes');
		if (function_exists('melodyschool_exists_visual_composer') && melodyschool_exists_visual_composer())
			add_action('melodyschool_action_shortcodes_list_vc','melodyschool_sc_countdown_reg_shortcodes_vc');
	}
}



/* Shortcode implementation
-------------------------------------------------------------------- */

//[trx_countdown date="" time=""]

if (!function_exists('melodyschool_sc_countdown')) {	
	function melodyschool_sc_countdown($atts, $content = null) {
		if (melodyschool_in_shortcode_blogger()) return '';
		extract(melodyschool_html_decode(shortcode_atts(array(
			// Individual params
			"date" => "",
			"time" => "",
			"style" => "1",
			"align" => "center",
			// Common params
			"id" => "",
			"class" => "",
			"css" => "",
			"animation" => "",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => "",
			"width" => "",
			"height" => ""
		), $atts)));
		if (empty($id)) $id = "sc_countdown_".str_replace('.', '', mt_rand());
		$class .= ($class ? ' ' : '') . melodyschool_get_css_position_as_classes($top, $right, $bottom, $left);
		$css .= melodyschool_get_css_dimensions_from_values($width, $height);
		if (empty($interval)) $interval = 1;
		wp_enqueue_script( 'melodyschool-jquery-plugin-script', melodyschool_get_file_url('js/countdown/jquery.plugin.js'), array('jquery'), null, true );
		wp_enqueue_script( 'melodyschool-countdown-script', melodyschool_get_file_url('js/countdown/jquery.countdown.js'), array('jquery'), null, true );
		$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '')
			. ' class="sc_countdown sc_countdown_style_' . esc_attr(max(1, min(2, $style))) . (!empty($align) && $align!='none' ? ' align'.esc_attr($align) : '') . (!empty($class) ? ' '.esc_attr($class) : '') .'"'
			. ($css ? ' style="'.esc_attr($css).'"' : '')
			. ' data-date="'.esc_attr(empty($date) ? date('Y-m-d') : $date).'"'
			. ' data-time="'.esc_attr(empty($time) ? '00:00:00' : $time).'"'
			. (!melodyschool_param_is_off($animation) ? ' data-animation="'.esc_attr(melodyschool_get_animation_classes($animation)).'"' : '')
			. '>'
				. ($align=='center' ? '<div class="sc_countdown_inner">' : '')
				. '<div class="sc_countdown_item sc_countdown_days">'
					. '<span class="sc_countdown_digits"><span></span><span></span><span></span></span>'
					. '<span class="sc_countdown_label">'.esc_html__('Days', 'trx_utils').'</span>'
				. '</div>'
				. '<div class="sc_countdown_separator">:</div>'
				. '<div class="sc_countdown_item sc_countdown_hours">'
					. '<span class="sc_countdown_digits"><span></span><span></span></span>'
					. '<span class="sc_countdown_label">'.esc_html__('Hours', 'trx_utils').'</span>'
				. '</div>'
				. '<div class="sc_countdown_separator">:</div>'
				. '<div class="sc_countdown_item sc_countdown_minutes">'
					. '<span class="sc_countdown_digits"><span></span><span></span></span>'
					. '<span class="sc_countdown_label">'.esc_html__('Minutes', 'trx_utils').'</span>'
				. '</div>'
				. '<div class="sc_countdown_separator">:</div>'
				. '<div class="sc_countdown_item sc_countdown_seconds">'
					. '<span class="sc_countdown_digits"><span></span><span></span></span>'
					. '<span class="sc_countdown_label">'.esc_html__('Seconds', 'trx_utils').'</span>'
				. '</div>'
				. '<div class="sc_countdown_placeholder hide"></div>'
				. ($align=='center' ? '</div>' : '')
			. '</div>';
		return apply_filters('melodyschool_shortcode_output', $output, 'trx_countdown', $atts, $content);
	}
	add_shortcode("trx_countdown", "melodyschool_sc_countdown");
}



/* Register shortcode in the internal SC Builder
-------------------------------------------------------------------- */
if ( !function_exists( 'melodyschool_sc_countdown_reg_shortcodes' ) ) {
	//add_action('melodyschool_action_shortcodes_list', 'melodyschool_sc_countdown_reg_shortcodes');
	function melodyschool_sc_countdown_reg_shortcodes() {
	
		melodyschool_sc_map("trx_countdown", array(
			"title" => esc_html__("Countdown", 'trx_utils'),
			"desc" => wp_kses_data( __("Insert countdown object", 'trx_utils') ),
			"decorate" => false,
			"container" => false,
			"params" => array(
				"date" => array(
					"title" => esc_html__("Date", 'trx_utils'),
					"desc" => wp_kses_data( __("Upcoming date (format: yyyy-mm-dd)", 'trx_utils') ),
					"value" => "",
					"format" => "yy-mm-dd",
					"type" => "date"
				),
				"time" => array(
					"title" => esc_html__("Time", 'trx_utils'),
					"desc" => wp_kses_data( __("Upcoming time (format: HH:mm:ss)", 'trx_utils') ),
					"value" => "",
					"type" => "text"
				),
				"style" => array(
					"title" => esc_html__("Style", 'trx_utils'),
					"desc" => wp_kses_data( __("Countdown style", 'trx_utils') ),
					"value" => "1",
					"type" => "checklist",
					"options" => melodyschool_get_list_styles(1, 2)
				),
				"align" => array(
					"title" => esc_html__("Alignment", 'trx_utils'),
					"desc" => wp_kses_data( __("Align counter to left, center or right", 'trx_utils') ),
					"divider" => true,
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
if ( !function_exists( 'melodyschool_sc_countdown_reg_shortcodes_vc' ) ) {
	//add_action('melodyschool_action_shortcodes_list_vc', 'melodyschool_sc_countdown_reg_shortcodes_vc');
	function melodyschool_sc_countdown_reg_shortcodes_vc() {
	
		vc_map( array(
			"base" => "trx_countdown",
			"name" => esc_html__("Countdown", 'trx_utils'),
			"description" => wp_kses_data( __("Insert countdown object", 'trx_utils') ),
			"category" => esc_html__('Content', 'trx_utils'),
			'icon' => 'icon_trx_countdown',
			"class" => "trx_sc_single trx_sc_countdown",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "date",
					"heading" => esc_html__("Date", 'trx_utils'),
					"description" => wp_kses_data( __("Upcoming date (format: yyyy-mm-dd)", 'trx_utils') ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "time",
					"heading" => esc_html__("Time", 'trx_utils'),
					"description" => wp_kses_data( __("Upcoming time (format: HH:mm:ss)", 'trx_utils') ),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "style",
					"heading" => esc_html__("Style", 'trx_utils'),
					"description" => wp_kses_data( __("Countdown style", 'trx_utils') ),
					"admin_label" => true,
					"class" => "",
					"value" => array_flip(melodyschool_get_list_styles(1, 2)),
					"type" => "dropdown"
				),
				array(
					"param_name" => "align",
					"heading" => esc_html__("Alignment", 'trx_utils'),
					"description" => wp_kses_data( __("Align counter to left, center or right", 'trx_utils') ),
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
			)
		) );
		
		class WPBakeryShortCode_Trx_Countdown extends MELODYSCHOOL_VC_ShortCodeSingle {}
	}
}
?>