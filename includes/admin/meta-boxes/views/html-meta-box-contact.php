<?php
/**
 * Displays the messenger metabox.
 *
 * @link       https://traxconn.com
 * @since      1.0.0
 * @package    Talkino
 * @subpackage Talkino/admin/meta-boxes/views
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Declare the values of show on mobile status.
$is_phone_show_only_on_mobile_status_checked = ( ! empty( $data['phone_show_only_on_mobile_status'] ) && 'on' === $data['phone_show_only_on_mobile_status'] ) ? 'checked' : '';

?>

<div>
	<div class="talkino-contact-details-section">
		<p class="talkino-job-title">
			<label><b><?php esc_html_e( 'Job Title:', 'talkino' ); ?></b></label><br>
			<input type="text" name="talkino_job_title" maxlength="25" class="talkino-job-title-input" value="<?php echo esc_attr( $data['job_title'] ); ?>"/><br>
			<label><i><?php esc_html_e( 'The job position of the agent.', 'talkino' ); ?></i></label>
		</p>
		<p class="talkino-welcome-message">
			<label><b><?php esc_html_e( 'Welcome Message:', 'talkino' ); ?></b></label><br>
			<textarea name="talkino_welcome_message" rows="4" cols="50" class="talkino-welcome-message-input"><?php echo esc_attr( $data['welcome_message'] ); ?></textarea><br>
			<label><i><?php esc_html_e( 'The welcome message of the agent for modern layout.', 'talkino' ); ?></i></label>
		</p>
	</div> 
	<div class="talkino-contact-details-section">
		<img class="talkino-admin-channel-icon" src="<?php echo esc_url( plugin_dir_url( TALKINO_BASE_NAME ) ); ?>assets/images/whatsapp-icon.png" />
		<p class="talkino-whatsapp-title">
			<label><b><?php esc_html_e( 'WhatsApp/ WhatsApp Business ID:', 'talkino' ); ?></b></label><br>
			<input type="tel" name="talkino_whatsapp_id" class="talkino-whatsapp-input" value="<?php echo esc_attr( $data['whatsapp_id'] ); ?>" /><br>
			<label><i><?php esc_html_e( 'Use full phone number in international format ( i.e. 12345678901 ) or leave it empty to deactivate WhatsApp.', 'talkino' ); ?></i></label>
		</p>
		<p class="talkino-whatsapp-prefilled-message-title">
			<label><b><?php esc_html_e( 'WhatsApp Pre-filled Message:', 'talkino' ); ?></b></label><br>
			<input type="text" name="talkino_whatsapp_pre_filled_message" class="talkino-whatsapp-prefilled-message-input" value="<?php echo esc_attr( $data['whatsapp_pre_filled_message'] ); ?>" /><br>
			<label><i><?php esc_html_e( 'A pre-filled message that will automatically appear in the text field of a WhatsApp chat.', 'talkino' ); ?></i></label>
		</p>
	</div>	
	<div class="talkino-contact-details-section">
		<img class="talkino-admin-channel-icon" src="<?php echo esc_url( plugin_dir_url( TALKINO_BASE_NAME ) ); ?>assets/images/messenger-icon.png" />
		<p class="talkino-messenger-title">
			<label><b><?php esc_html_e( 'Facebook Username/ Page Name:', 'talkino' ); ?></b></label><br>
			<input type="text" name="talkino_facebook_id" class="talkino-messenger-input" value="<?php echo esc_attr( $data['facebook_id'] ); ?>" /><br>
			<label><i><?php esc_html_e( 'A username is the web address for your profile or Page. Leave it empty if you want to deactivate it.', 'talkino' ); ?></i></label>
		</p>
	</div>	
	<div class="talkino-contact-details-section">
		<img class="talkino-admin-channel-icon" src="<?php echo esc_url( plugin_dir_url( TALKINO_BASE_NAME ) ); ?>assets/images/telegram-icon.png" />
		<p class="talkino-telegram-title">
			<label><b><?php esc_html_e( 'Telegram Username:', 'talkino' ); ?></b></label><br>
			<input type="text" name="talkino_telegram_id" class="talkino-telegram-input" value="<?php echo esc_attr( $data['telegram_id'] ); ?>" /><br>
			<label><i><?php esc_html_e( 'Enter Telegram username. Leave it empty if you want to deactivate it.', 'talkino' ); ?></i></label>
		</p>
	</div>
	<div class="talkino-contact-details-section">
		<img class="talkino-admin-channel-icon" src="<?php echo esc_url( plugin_dir_url( TALKINO_BASE_NAME ) ); ?>assets/images/phone-icon.png" />
		<p class="talkino-phone-title">
			<label><b><?php esc_html_e( 'Phone Number:', 'talkino' ); ?></b></label><br>
			<input type="tel" name="talkino_phone_number" class="talkino-phone-input" value="<?php echo esc_attr( $data['phone_number'] ); ?>" /><br>
			<input name="talkino_phone_show_only_on_mobile_status" type="hidden" value='off' />
			<input id="talkino-phone-show-only-on-mobile-status" name="talkino_phone_show_only_on_mobile_status" type="checkbox" <?php echo esc_attr( $is_phone_show_only_on_mobile_status_checked ); ?> value='on' />
			<label class="show-only-on-mobile-view"><?php esc_html_e( 'Show only on mobile view?', 'talkino' ); ?></label><br>
			<label><i><?php esc_html_e( 'Key in phone number or leave it empty if you want to deactivate it. Note: Only shown on mobile or tablet devices.', 'talkino' ); ?></i></label>
		</p>
	</div>	
	<div class="talkino-contact-details-section">
		<img class="talkino-admin-channel-icon" src="<?php echo esc_url( plugin_dir_url( TALKINO_BASE_NAME ) ); ?>assets/images/email-icon.png" />
		<p class="talkino-email-title">
			<label><b><?php esc_html_e( 'Email:', 'talkino' ); ?></b></label><br>
			<input type="email" name="talkino_email" class="talkino-email-input" value="<?php echo esc_attr( $data['email'] ); ?>" /><br>
			<label><i><?php esc_html_e( 'Personal or company email address. Leave it empty if you want to deactivate it.', 'talkino' ); ?></i></label>
		</p>
	</div>
</div>
