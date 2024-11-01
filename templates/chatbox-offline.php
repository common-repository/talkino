<?php
/**
 * Displays the part of chatbox when offline status.
 *
 * @link       https://traxconn.com
 * @since      1.0.0
 * @package    Talkino
 * @subpackage Talkino/templates
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( is_plugin_active( 'wpml-string-translation/plugin.php' ) ) {

	$talkino_chat_subtitle       = apply_filters( 'wpml_translate_single_string', get_option( 'talkino_chatbox_offline_subtitle' ), 'talkino', 'Chatbox Offline Subtitle' );
	$talkino_offline_message     = apply_filters( 'wpml_translate_single_string', get_option( 'talkino_offline_message' ), 'talkino', 'Offline Message' );
	$talkino_chatbox_button_text = apply_filters( 'wpml_translate_single_string', get_option( 'talkino_chatbox_button_text' ), 'talkino', 'Chatbox Button Text' );

} else {

	$talkino_chat_subtitle       = get_option( 'talkino_chatbox_offline_subtitle' );
	$talkino_offline_message     = get_option( 'talkino_offline_message' );
	$talkino_chatbox_button_text = get_option( 'talkino_chatbox_button_text' );
}

?>
<input type="checkbox" id="check"> 
<label class="talkino-chat-btn" for="check"> 
	<div class="talkino-icon">
		<i class="dashicons <?php echo esc_html( get_option( 'talkino_chatbox_icon' ) ); ?> round talkino"></i>
	</div>
	<div class="talkino-rectangle-label">
		<?php echo esc_html( $talkino_chatbox_button_text ); ?><i class="dashicons <?php echo esc_html( get_option( 'talkino_chatbox_icon' ) ); ?> rectangle talkino"></i>
	</div>
</label>
<div class="talkino-chat-wrapper">
	<div class="talkino-chat-title">
		<b><?php esc_html_e( 'Offline', 'talkino' ); ?></b>
		<label class="talkino-chat-close" for="check">
				<i class="dashicons dashicons-minus talkino-chat-close-btn"></i>
		</label> 
	</div>
	<div class="talkino-chat-subtitle"> 
		<span><?php echo esc_html( $talkino_chat_subtitle ); ?></span> 
	</div>
	<div class="talkino-agent-wrapper">
		<div class="talkino-information-wrapper">
			<div class="talkino-notice"><i><?php echo esc_html( $talkino_offline_message ); ?></i></div>
		</div>
		<?php
		// Add back button to agent wrapper when talkino bundle is installed, typebot is activated and typebot link is not empty
		if ( is_plugin_active( 'talkino-bundle/talkino-bundle.php' ) && get_option( 'talkino_typebot_status' ) === 'on' && ! empty( get_option( 'talkino_typebot_link' ) ) ) {
			?>
			<button type="button" id="talkino-typebot-back-button" class="talkino-typebot-back-button" name="talkino_typebot_back_button"/><?php esc_html_e( 'Back', 'talkino' ); ?></button>
		<?php
		}
		?>
	</div>

	<?php
	// Add typebot wrapper when talkino bundle is installed, typebot is activated and typebot link is not empty
	if ( is_plugin_active( 'talkino-bundle/talkino-bundle.php' ) && get_option( 'talkino_typebot_status' ) === 'on' && ! empty( get_option( 'talkino_typebot_link' ) ) ) {
	?>
		<div class="talkino-typebot-wrapper">
			<iframe class="talkino-typebot-iframe" src="https://viewer.typebot.io/<?php echo wp_kses_post( get_option( 'talkino_typebot_link' ) );?>" width="100%" height="350px" style="border: none"></iframe>
			<button type="button" id="talkino-start-chat-button" class="talkino-start-chat-button" name="talkino_start_chat_button"/><?php esc_html_e( 'Start Chat', 'talkino' ); ?></button>
		</div>
	<?php
	}

	if ( get_option( 'talkino_credit' ) === 'on' ) {
	?>
	<div class="talkino-footer-wrapper">
		<a class="talkino-credit-link" href="https://wordpress.org/plugins/talkino/" rel="nofollow" target=”_blank”>Powered by Talkino</a> 
	</div>
	<?php
	}
	?>
</div>
