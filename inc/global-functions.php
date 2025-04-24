<?php
function phone_8420($phone_no) {
	return preg_replace('/^84/', '0', $phone_no);
}

function phone_0284($phone_no) {
	return preg_replace('/^0/', '84', $phone_no);
}

function phone_number_format($phone_no, $sep=' ') {
	$phone_no = phone_8420($phone_no);
	$format_phone =
    substr($phone_no, -10, -7) . $sep .
    substr($phone_no, -7, -4) . $sep .
    substr($phone_no, -4);
	return $format_phone;
}

function sanitize_phone_number($number) {
	$number = preg_replace('/\D/', '', $number);
	$number = phone_8420($number);
	if(preg_match('/^0\d{9}$/', $number)) {
		return $number;
	}
	return '';
}

function debug($var) {
	?>
	<pre><?php print_r($var); ?></pre>
	<?php
}

function debug_log($var) {
	error_log(print_r($var,true));
}

function unyson_exists() {
	return (defined('FW')) ? true : false;
}

function elementor_exists() {
	return did_action( 'elementor/loaded' );
}

function base64url_encode( $data ){
  return rtrim( strtr( base64_encode( $data ), '+/', '-_'), '=');
}

function base64url_decode( $data ){
  return base64_decode( strtr( $data, '-_', '+/') . str_repeat('=', 3 - ( 3 + strlen( $data )) % 4 ));
}

function wp_current_url() {
	global $wp;
	return home_url( add_query_arg( array(), $wp->request ) );
}

function unparse_url($parsed_url) {

	$scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : 'https://';

	$host     = isset($parsed_url['host']) ? $parsed_url['host'] : '';

	$port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';

	$user     = isset($parsed_url['user']) ? $parsed_url['user'] : '';

	$pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass']  : '';

	$pass     = ($user || $pass) ? "$pass@" : '';

	$path     = isset($parsed_url['path']) ? $parsed_url['path'] : '';

	$query    = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';

	$fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';

	return "$scheme$user$pass$host$port$path$query$fragment";

}

function get_youtube_id($url) {
	$pattern = '#^(?:https?://)?(?:www\.)?(?:youtu\.be/|youtube(?:-nocookie)?\.com(?:/embed/|/v/|/watch\?v=))([\w-]{10,12})#';
	$result = preg_match($pattern, $url, $matches);
	if (false !== boolval($result)) {
		return $matches[1];
	}
	return sanitize_text_field($url);
}

function unescape( $str ) {
	// return str_replace(
	// 	array ( '&lt;', '&gt;', '&quot;', '&amp;', '&nbsp;', '&amp;nbsp;' ),
	// 	array ( '<', '>', '"', '&', ' ', ' ' ),
	// 	$str
	// );
	return html_entity_decode( $str, ENT_QUOTES, 'UTF-8' );
}

function wp_format_content($raw_string='') {
	global $wp_embed;
	
	$content = wp_kses_post( $raw_string );

	$content = do_blocks($content);
	$content = wptexturize($content);
	$content = convert_smilies($content);
	$content = convert_chars($content);
	$wp_embed->run_shortcode($content);
	$content = wpautop($content);
	$content = shortcode_unautop($content);
	$content = prepend_attachment($content);
	$content = wp_filter_content_tags($content);
	$content = do_shortcode($content);
	$content = wp_replace_insecure_home_url($content);
	$content = $wp_embed->autoembed($content);

	return $content;
}

function wp_get_the_content($content) {
	return apply_filters( 'the_content', $content );
}

function wp_do_shortcode( $tag, array $atts = array(), $content = null ) {
	global $shortcode_tags;

	if ( ! isset( $shortcode_tags[$tag] ) ) {
		return false;
	}

	return call_user_func( $shortcode_tags[$tag], $atts, $content, $tag );
}

function has_role($role, $user=null) {
	if($user==null) $user = wp_get_current_user();
	if($user instanceof WP_User) {
		return in_array($role, (array) $user->roles);
	} else {
		return false;
	}
}
