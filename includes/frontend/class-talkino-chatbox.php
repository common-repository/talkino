<?php
/**
 * The class to handle the chatbox.
 *
 * @link       https://traxconn.com
 * @since      1.0.0
 * @package    Talkino
 * @subpackage Talkino/includes/frontend
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The class to handle the chatbox.
 *
 * @package    Talkino
 * @subpackage Talkino/includes/frontend
 * @author     Traxconn <mail@traxconn.com>
 */
class Talkino_Chatbox {

    /**
	 * Determine whether there are agents online.
	 *
	 * @since     2.0.0
	 * @access    private
	 * @var       bool    $is_agents_online    Whether there are agents online.
	 */
    private $is_agents_online;

    /**
	 * Determine whether global schedule is online.
	 *
	 * @since     2.0.0
	 * @access    private
	 * @var       bool    $is_global_schedule_online    Whether global schedule is online.
	 */
    private $is_global_schedule_online;

	/**
	 * Initialize the chatbox.
	 *
	 * @since    1.0.0
	 */
	public function chatbox_init() {

		// Declare the class to load html to render chatbox.
		$talkino_file_loader     = new Talkino_File_Loader();
		$talkino_agent_manager   = new Talkino_Agent_Manager();
		$talkino_utility_manager = new Talkino_Utility_Manager();
        $talkino_ordering_manager = new Talkino_Ordering_Manager();

		$this->is_global_schedule_online = false;
        $this->is_agents_online = true;
        $is_user_block = false;
        $show_chatbox = false;
        $data         = array();

		// Declare the bundle class to load html to render chatbox.
		if ( is_plugin_active( 'talkino-bundle/talkino-bundle.php' ) ) {

            $talkino_bundle_file_loader   = new Talkino_Bundle_File_Loader();
			$talkino_bundle_scheduler = new Talkino_Bundle_Scheduler();
            $talkino_bundle_blocker = new Talkino_Bundle_Blocker();

			// Call the function to check schedule online status via current weekday and time.
			$this->is_global_schedule_online = $talkino_bundle_scheduler->check_global_schedule_online_status();

            // Call the function to check whether to block user.
            $is_user_block = $talkino_bundle_blocker->is_user_block();

		}

        // Direct layout.
        if ( get_option( 'talkino_chatbox_layout' ) == 'direct' ){

            // Call to query agent data by direct layout.
            $talkino_agent_manager->query_agent_direct_data();

            // Retrieve the channel output from agents.
            $whatsapp_output = $talkino_agent_manager->get_whatsapp_output();
            $messenger_output = $talkino_agent_manager->get_messenger_output();
            $telegram_output = $talkino_agent_manager->get_telegram_output();
            $phone_output    = $talkino_agent_manager->get_phone_output();
            $email_output    = $talkino_agent_manager->get_email_output();

            // Check whether there is no agent.
            if ( $whatsapp_output === '' && $messenger_output === '' && $telegram_output === '' && $phone_output === '' && $email_output === '' ) {
                $this->is_agents_online = false;
            }

            // Sort ordering of chat channels.
            $data = $talkino_ordering_manager->sort_chat_channels_ordering( $whatsapp_output, $messenger_output, $telegram_output, $phone_output, $email_output );
        
        }

        // Modern layout.
        else {

            // Call to query agent data by modern layout.
            $data = $talkino_agent_manager->query_agent_modern_data();

            // Check whether there is no agent.
            if ( $data == '' ) {
                $this->is_agents_online = false;
            }

        }

		// Check whether is mobile.
		if ( wp_is_mobile() ) {
			if ( get_option( 'talkino_show_on_mobile' ) === 'on' ) {
				$show_chatbox = true;
			}

		} else {
			if ( get_option( 'talkino_show_on_desktop' ) === 'on' ) {
				$show_chatbox = true;
			}
		}

		// Check whether to display or hide chatbox on pages
		// Get the page id.
		$page_id = get_queried_object_id();

		if ( ! empty( get_option( 'talkino_chatbox_exclude_pages' ) ) && in_array( $page_id, get_option( 'talkino_chatbox_exclude_pages' ) ) ) {
			$show_chatbox = false;
		}

		if ( get_option( 'talkino_show_on_post' ) === 'off' && $talkino_utility_manager->is_blog() ) {
			$show_chatbox = false;
		}

		// Check whether woocommerce is activated and whether is woocommerce page.
		if ( $talkino_utility_manager->is_woocommerce_activated() && get_option( 'talkino_show_on_woocommerce_pages' ) === 'off' && is_woocommerce() ) {
			$show_chatbox = false;
		}

		// Check whether is search page.
		if ( is_search() && get_option( 'talkino_show_on_search' ) === 'off' ) {
			$show_chatbox = false;
		}

        // Check whether is 404 page.
        if ( is_404() && get_option( 'talkino_show_on_404' ) === 'off' ) {
			$show_chatbox = false;
		}

        // Check whether is logged in users and user visibility is for logged in users only.
        if ( ! is_user_logged_in() && get_option( 'talkino_user_visibility' ) === 'loggedin' ) {
            $show_chatbox = false;
        }

        // Check whether the user's country is blocked.
        if ( is_plugin_active( 'talkino-bundle/talkino-bundle.php' ) && get_option( 'talkino_activate_country_block' ) === 'on' && $is_user_block == true ) {
            $show_chatbox = false;
        }

		// Ensure that it is show on chatbox.
		if ( true === $show_chatbox ) {
			// Has agents, global online status is online and schedule is available.
			if ( ( true === $this->is_agents_online && ! is_plugin_active( 'talkino-bundle/talkino-bundle.php' ) && get_option( 'talkino_global_online_status' ) === 'online' ) || ( true === $this->is_agents_online && get_option( 'talkino_global_online_status' ) === 'online' && true === $this->is_global_schedule_online ) ) {
				$talkino_file_loader->load_chatbox_template_file( 'chatbox-online.php', $data );

			// Has agents, global online status is away and schedule is available.
			} elseif ( ( true === $this->is_agents_online && ! is_plugin_active( 'talkino-bundle/talkino-bundle.php' ) && get_option( 'talkino_global_online_status' ) === 'away' ) || ( true === $this->is_agents_online && get_option( 'talkino_global_online_status' ) === 'away' && true === $this->is_global_schedule_online ) ) {
				$talkino_file_loader->load_chatbox_template_file( 'chatbox-away.php', $data );

			// No agents, global online status if offline or schedule is not available.
			} else {
				// Function to show contact form when offline.
				if ( is_plugin_active( 'talkino-bundle/talkino-bundle.php' ) && get_option( 'talkino_contact_form_status' ) === 'on' ) {
					$talkino_bundle_file_loader->load_chatbox_template_file( 'contact-form.php', $data );
				
                } else {
					$talkino_file_loader->load_chatbox_template_file( 'chatbox-offline.php', $data );
				}
			}

			// Call the function to render chatbox style.
			$this->render_chatbox_style();
		}

	}

	/**
	 * Render the chatbox using setting style options.
	 *
	 * @since    1.0.0
	 */
	public function render_chatbox_style() {

        $wrapper_animation = '';

        // Animation style.
        if ( get_option( 'talkino_chatbox_animation' ) === 'fadein' ) {
            $wrapper_animation = 'animation: talkino-fade-in 1.5s;';
        }
        else if ( get_option( 'talkino_chatbox_animation' ) === 'slideup' ) {
            $wrapper_animation = 'animation: talkino-slide-up 1.5s;';
        }

        // Typebot style.
        // When talkino bundle is installed, typebot is activated and typebot link is not empty
        if ( is_plugin_active( 'talkino-bundle/talkino-bundle.php' ) && get_option( 'talkino_typebot_status' ) === 'on' && ! empty( get_option( 'talkino_typebot_link' ) ) ) {
           
            echo '<style>

            .talkino-agent-wrapper {
                display: none;
            }  
            
            .talkino-start-chat-button, .talkino-typebot-back-button {
                background-color: ' . esc_attr( get_option( 'talkino_chatbox_button_color' ) ) . ' !important;
                background: ' . esc_attr( get_option( 'talkino_chatbox_button_color' ) ) . ' !important;
                color: ' . esc_attr( get_option( 'talkino_chatbox_button_text_color' ) ) . ' !important;
                border-color: ' . esc_attr( get_option( 'talkino_chatbox_button_color' ) ) . ' !important;
            }

            .talkino-start-chat-button:hover, .talkino-typebot-back-button:hover {
                background-color: ' . esc_attr( get_option( 'talkino_chatbox_button_color' ) ) . ' !important;
                background: ' . esc_attr( get_option( 'talkino_chatbox_button_color' ) ) . ' !important;
                color: ' . esc_attr( get_option( 'talkino_chatbox_button_text_color' ) ) . ' !important;
                border-color: ' . esc_attr( get_option( 'talkino_chatbox_button_color' ) ) . ' !important;
            }

            </style>';

        } else {

            echo '<style>

            .talkino-agent-wrapper {
                display: block;
            }            

            </style>';

        }
        
        // Style for every scenario.
        echo '<style>

        .talkino-chat-btn {
            z-index: ' . esc_attr( intval( get_option( 'talkino_chatbox_z_index' ) ) ) . ' !important;
        }

        .talkino-chat-wrapper {
            z-index: ' . esc_attr( intval( get_option( 'talkino_chatbox_z_index' ) ) ) . ' !important;
        }

        .talkino-information-wrapper {
            scrollbar-color: #c1c1c1 ' . esc_attr( get_option( 'talkino_chatbox_background_color' ) ) . ';
        }

        .talkino-information-wrapper::-webkit-scrollbar {
            background: ' . esc_attr( get_option( 'talkino_chatbox_background_color' ) ) . ';
        }

        .talkino-chat-close {
            color: ' . esc_attr( get_option( 'talkino_chatbox_title_color' ) ) . ';
        }
        
        .talkino-chat-subtitle {
            color: ' . esc_attr( get_option( 'talkino_chatbox_subtitle_color' ) ) . ';
        }

        .talkino-chat-direct-information {
            background-color: ' . esc_attr( get_option( 'talkino_agent_field_background_color' ) ) . ';
        }

        .talkino-chat-direct-information:hover {
            background-color: ' . esc_attr( get_option( 'talkino_agent_field_hover_background_color' ) ) . ';
        }

        .talkino-chat-direct-information-offline {
            background-color: ' . esc_attr( get_option( 'talkino_agent_field_background_color' ) ) . ';
        }

        .talkino-chat-direct-information-offline:hover {
            background-color: ' . esc_attr( get_option( 'talkino_agent_field_hover_background_color' ) ) . ';
        }

        .talkino-chat-modern-information {
            background-color: ' . esc_attr( get_option( 'talkino_agent_field_background_color' ) ) . ';
        }

        .talkino-chat-modern-information:hover {
            background-color: ' . esc_attr( get_option( 'talkino_agent_field_hover_background_color' ) ) . ';
        }

        .talkino-chat-modern-information-offline {
            background-color: ' . esc_attr( get_option( 'talkino_agent_field_background_color' ) ) . ';
        }

        .talkino-chat-modern-information-offline:hover {
            background-color: ' . esc_attr( get_option( 'talkino_agent_field_hover_background_color' ) ) . ';
        }

        .talkino-agent-profile-wrapper {
            background: ' . esc_attr( get_option( 'talkino_chatbox_background_color' ) ) . ';
        }

        .talkino-talk-bubble {
            background-color: ' . esc_attr( get_option( 'talkino_bubble_background_color' ) ) . ';
        }

        .talkino-tri-right.left-top:after {
            border-color: ' . esc_attr( get_option( 'talkino_bubble_background_color' ) ) . ' transparent transparent transparent;
        }

        .talkino-back-button {
            background-color: ' . esc_attr( get_option( 'talkino_chatbox_button_color' ) ) . ' !important;
            background: ' . esc_attr( get_option( 'talkino_chatbox_button_color' ) ) . ' !important;
            color: ' . esc_attr( get_option( 'talkino_chatbox_button_text_color' ) ) . ' !important;
            border-color: ' . esc_attr( get_option( 'talkino_chatbox_button_color' ) ) . ' !important;
        }

        .talkino-back-button:hover {
            background-color: ' . esc_attr( get_option( 'talkino_chatbox_button_color' ) ) . ' !important;
            background: ' . esc_attr( get_option( 'talkino_chatbox_button_color' ) ) . ' !important;
            color: ' . esc_attr( get_option( 'talkino_chatbox_button_text_color' ) ) . ' !important;
            border-color: ' . esc_attr( get_option( 'talkino_chatbox_button_color' ) ) . ' !important;
        }

        span.talkino-chat-name {
            color: ' . esc_attr( get_option( 'talkino_agent_name_text_color' ) ) . ';
        }

        span.talkino-chat-job-title {
            color: ' . esc_attr( get_option( 'talkino_agent_job_title_text_color' ) ) . ';
        }

        span.talkino-chat-channel {
            color: ' . esc_attr( get_option( 'talkino_agent_channel_text_color' ) ) . ';
        }

        .talkino-notice {
            color: ' . esc_attr( get_option( 'talkino_chatbox_subtitle_color' ) ) . ';
        }

        #talkino-contact-form-notice {
            color: ' . esc_attr( get_option( 'talkino_contact_form_notice_text_color' ) ) . ';
        }

        .talkino-google-recaptcha-notice {
            color: ' . esc_attr( get_option( 'talkino_google_recaptcha_notice_text_color' ) ) . ';
        }

        .talkino-google-recaptcha-link ,
        .talkino-google-recaptcha-link:hover {
            color: ' . esc_attr( get_option( 'talkino_google_recaptcha_link_text_color' ) ) . ';
        }

        #talkino-contact-form-submit-button {
            background-color: ' . esc_attr( get_option( 'talkino_chatbox_button_color' ) ) . ';
            background: ' . esc_attr( get_option( 'talkino_chatbox_button_color' ) ) . ';
            color: ' . esc_attr( get_option( 'talkino_chatbox_button_text_color' ) ) . ';
            border-color: ' . esc_attr( get_option( 'talkino_chatbox_button_color' ) ) . ';
        }

        .talkino-credit-link,.talkino-credit-link:hover {
            color: ' . esc_attr( get_option( 'talkino_credit_text_color' ) ) . ';
        }

        </style>';

		// Chatbox under round style.
		if ( get_option( 'talkino_chatbox_style' ) === 'round' ) {
			if ( get_option( 'talkino_chatbox_position' ) === 'left' ) {
				if ( ( true === $this->is_agents_online && ! is_plugin_active( 'talkino-bundle/talkino-bundle.php' ) && get_option( 'talkino_global_online_status' ) === 'online' ) || ( true === $this->is_agents_online && get_option( 'talkino_global_online_status' ) === 'online' && true === $this->is_global_schedule_online ) ) {

					echo '<style>
                    .talkino-chat-btn {
                        background-color: ' . esc_attr( get_option( 'talkino_chatbox_online_theme_color' ) ) . ';
                        width: 50px;
                        height: 50px;
                        left: 15px;
                        bottom: 30px;
                        border-radius: 50px;
                    }

                    .round.talkino {
                        color: ' . esc_attr( get_option( 'talkino_chatbox_online_icon_color' ) ) . ';
                    }

                    .talkino-rectangle-label {
                        display: none;
                    }

                    .talkino-chat-wrapper {
                        background-color: ' . esc_attr( get_option( 'talkino_chatbox_background_color' ) ) . ';
                        left: 20px;
                        border-radius: 10px 10px 0 0;
                        ' . esc_attr( $wrapper_animation ) . '
                    }

                    .talkino-chat-title {
                        background-color: ' . esc_attr( get_option( 'talkino_chatbox_online_theme_color' ) ) . ';
                        color: ' . esc_attr( get_option( 'talkino_chatbox_title_color' ) ) . ';
                        border-radius: 10px 10px 0 0;
                    }

                    </style>';

				} elseif ( ( true === $this->is_agents_online && ! is_plugin_active( 'talkino-bundle/talkino-bundle.php' ) && get_option( 'talkino_global_online_status' ) === 'away' ) || ( true === $this->is_agents_online && get_option( 'talkino_global_online_status' ) === 'away' && true === $this->is_global_schedule_online ) ) {

					echo '<style>
                    .talkino-chat-btn {
                        background-color: ' . esc_attr( get_option( 'talkino_chatbox_away_theme_color' ) ) . ';
                        width: 50px;
                        height: 50px;
                        left: 15px;
                        bottom: 30px;
                        border-radius: 50px;
                    }

                    .round.talkino {
                        color: ' . esc_attr( get_option( 'talkino_chatbox_away_icon_color' ) ) . ';
                    }

                    .talkino-rectangle-label {
                        display: none;
                    }

                    .talkino-chat-wrapper {
                        background-color: ' . esc_attr( get_option( 'talkino_chatbox_background_color' ) ) . ';
                        left: 20px;
                        border-radius: 10px 10px 0 0;
                        ' . esc_attr( $wrapper_animation ) . '
                    }

                    .talkino-chat-title {
                        background-color: ' . esc_attr( get_option( 'talkino_chatbox_away_theme_color' ) ) . ';
                        color: ' . esc_attr( get_option( 'talkino_chatbox_title_color' ) ) . ';
                        border-radius: 10px 10px 0 0;
                    }

                    </style>';

				} else {

					echo '<style>
                    .talkino-chat-btn {
                        background-color: ' . esc_attr( get_option( 'talkino_chatbox_offline_theme_color' ) ) . ';
                        width: 50px;
                        height: 50px;
                        left: 15px;
                        bottom: 30px;
                        border-radius: 50px;
                    }

                    .round.talkino {
                        color: ' . esc_attr( get_option( 'talkino_chatbox_offline_icon_color' ) ) . ';
                    }

                    .talkino-rectangle-label {
                        display: none;
                    }

                    .talkino-chat-wrapper {
                        background-color: ' . esc_attr( get_option( 'talkino_chatbox_background_color' ) ) . ';
                        left: 20px;
                        border-radius: 10px 10px 0 0;
                        ' . esc_attr( $wrapper_animation ) . '
                    }

                    .talkino-chat-title {
                        background-color: ' . esc_attr( get_option( 'talkino_chatbox_offline_theme_color' ) ) . ';
                        color: ' . esc_attr( get_option( 'talkino_chatbox_title_color' ) ) . ';
                        border-radius: 10px 10px 0 0;
                    }

                    </style>';

				}

			} else { // Right position.
				if ( ( true === $this->is_agents_online && ! is_plugin_active( 'talkino-bundle/talkino-bundle.php' ) && get_option( 'talkino_global_online_status' ) === 'online' ) || ( true === $this->is_agents_online && get_option( 'talkino_global_online_status' ) === 'online' && true === $this->is_global_schedule_online ) ) {

					echo '<style>
                    .talkino-chat-btn {
                        background-color: ' . esc_attr( get_option( 'talkino_chatbox_online_theme_color' ) ) . ';
                        width: 50px;
                        height: 50px;
                        right: 15px;
                        bottom: 30px;
                        border-radius: 50px;
                    }

                    .round.talkino {
                        color: ' . esc_attr( get_option( 'talkino_chatbox_online_icon_color' ) ) . ';
                    }

                    .talkino-rectangle-label {
                        display: none;
                    }

                    .talkino-chat-wrapper {
                        background-color: ' . esc_attr( get_option( 'talkino_chatbox_background_color' ) ) . ';
                        right: 20px;
                        border-radius: 10px 10px 0 0;
                        ' . esc_attr( $wrapper_animation ) . '
                    }

                    .talkino-chat-title {
                        background-color: ' . esc_attr( get_option( 'talkino_chatbox_online_theme_color' ) ) . ';
                        color: ' . esc_attr( get_option( 'talkino_chatbox_title_color' ) ) . ';
                        border-radius: 10px 10px 0 0;
                    }

                    </style>';

				} elseif ( ( true === $this->is_agents_online && ! is_plugin_active( 'talkino-bundle/talkino-bundle.php' ) && get_option( 'talkino_global_online_status' ) === 'away' ) || ( true === $this->is_agents_online && get_option( 'talkino_global_online_status' ) === 'away' && true === $this->is_global_schedule_online ) ) {

					echo '<style>
                    .talkino-chat-btn {
                        background-color: ' . esc_attr( get_option( 'talkino_chatbox_away_theme_color' ) ) . ';
                        width: 50px;
                        height: 50px;
                        right: 15px;
                        bottom: 30px;
                        border-radius: 50px;
                    }

                    .round.talkino {
                        color: ' . esc_attr( get_option( 'talkino_chatbox_away_icon_color' ) ) . ';
                    }

                    .talkino-rectangle-label {
                        display: none;
                    }

                    .talkino-chat-wrapper {
                        background-color: ' . esc_attr( get_option( 'talkino_chatbox_background_color' ) ) . ';
                        right: 20px;
                        border-radius: 10px 10px 0 0;
                        ' . esc_attr( $wrapper_animation ) . '
                    }

                    .talkino-chat-title {
                        background-color: ' . esc_attr( get_option( 'talkino_chatbox_away_theme_color' ) ) . ';
                        color: ' . esc_attr( get_option( 'talkino_chatbox_title_color' ) ) . ';
                        border-radius: 10px 10px 0 0;
                    }

                    </style>';

				} else {

					echo '<style>
                    .talkino-chat-btn {
                        background-color: ' . esc_attr( get_option( 'talkino_chatbox_offline_theme_color' ) ) . ';
                        width: 50px;
                        height: 50px;
                        right: 15px;
                        bottom: 30px;
                        border-radius: 50px;
                    }

                    .round.talkino {
                        color: ' . esc_attr( get_option( 'talkino_chatbox_offline_icon_color' ) ) . ';
                    }

                    .talkino-rectangle-label {
                        display: none;
                    }

                    .talkino-chat-wrapper {
                        background-color: ' . esc_attr( get_option( 'talkino_chatbox_background_color' ) ) . ';
                        right: 20px;
                        border-radius: 10px 10px 0 0;
                        ' . esc_attr( $wrapper_animation ) . '
                    }

                    .talkino-chat-title {
                        background-color: ' . esc_attr( get_option( 'talkino_chatbox_offline_theme_color' ) ) . ';
                        color: ' . esc_attr( get_option( 'talkino_chatbox_title_color' ) ) . ';
                        border-radius: 10px 10px 0 0;
                    }

                    </style>';

				}
			}
		}

		// Chatbox under rectangle style.
		else {
			if ( get_option( 'talkino_chatbox_position' ) === 'left' ) {
				if ( ( true === $this->is_agents_online && ! is_plugin_active( 'talkino-bundle/talkino-bundle.php' ) && get_option( 'talkino_global_online_status' ) === 'online' ) || ( true === $this->is_agents_online && get_option( 'talkino_global_online_status' ) === 'online' && true === $this->is_global_schedule_online ) ) {

					echo '<style>
                    .talkino-chat-btn {
                        background-color: ' . esc_attr( get_option( 'talkino_chatbox_online_theme_color' ) ) . ';
                        min-width: 230px;
                        height: 40px;
                        left: 15px;
                        bottom: 0;
                        border-radius: 5px 5px 0 0;
                    }

                    .talkino-rectangle-label {
                        color: ' . esc_attr( get_option( 'talkino_chatbox_online_icon_color' ) ) . ';
                    }

                    .dashicons.' . str_replace( ' ', '.', esc_attr( get_option( 'talkino_chatbox_icon' ) ) ) . '.round {
                        display: none;
                    }

                    .talkino-chat-wrapper {
                        background-color: ' . esc_attr( get_option( 'talkino_chatbox_background_color' ) ) . ';
                        left: 20px;
                        border-radius: 0;
                        ' . esc_attr( $wrapper_animation ) . '
                    }

                    .talkino-chat-title {
                        background-color: ' . esc_attr( get_option( 'talkino_chatbox_online_theme_color' ) ) . ';
                        color: ' . esc_attr( get_option( 'talkino_chatbox_title_color' ) ) . ';
                        border-radius: 0;
                    }

                    </style>';

				} elseif ( ( true === $this->is_agents_online && ! is_plugin_active( 'talkino-bundle/talkino-bundle.php' ) && get_option( 'talkino_global_online_status' ) === 'away' ) || ( true === $this->is_agents_online && get_option( 'talkino_global_online_status' ) === 'away' && true === $this->is_global_schedule_online ) ) {

					echo '<style>
                    .talkino-chat-btn {
                        background-color: ' . esc_attr( get_option( 'talkino_chatbox_away_theme_color' ) ) . ';
                        min-width: 230px;
                        height: 40px;
                        left: 15px;
                        bottom: 0;
                        border-radius: 5px 5px 0 0;
                    }

                    .talkino-rectangle-label {
                        color: ' . esc_attr( get_option( 'talkino_chatbox_away_icon_color' ) ) . ';
                    }

                    .dashicons.' . str_replace( ' ', '.', esc_attr( get_option( 'talkino_chatbox_icon' ) ) ) . '.round {
                        display: none;
                    }

                    .talkino-chat-wrapper {
                        background-color: ' . esc_attr( get_option( 'talkino_chatbox_background_color' ) ) . ';
                        left: 20px;
                        border-radius: 0;
                        ' . esc_attr( $wrapper_animation ) . '
                    }

                    .talkino-chat-title {
                        background-color: ' . esc_attr( get_option( 'talkino_chatbox_away_theme_color' ) ) . ';
                        color: ' . esc_attr( get_option( 'talkino_chatbox_title_color' ) ) . ';
                        border-radius: 0;
                    }

                    </style>';

				} else {

					echo '<style>
                    .talkino-chat-btn {
                        background-color: ' . esc_attr( get_option( 'talkino_chatbox_offline_theme_color' ) ) . ';
                        min-width: 230px;
                        height: 40px;
                        left: 15px;
                        bottom: 0;
                        border-radius: 5px 5px 0 0;
                    }

                    .talkino-rectangle-label {
                        color: ' . esc_attr( get_option( 'talkino_chatbox_offline_icon_color' ) ) . ';
                    }

                    .dashicons.' . str_replace( ' ', '.', esc_attr( get_option( 'talkino_chatbox_icon' ) ) ) . '.round {
                        display: none;
                    }

                    .talkino-chat-wrapper {
                        background-color: ' . esc_attr( get_option( 'talkino_chatbox_background_color' ) ) . ';
                        left: 20px;
                        border-radius: 0;
                        ' . esc_attr( $wrapper_animation ) . '
                    }

                    .talkino-chat-title {
                        background-color: ' . esc_attr( get_option( 'talkino_chatbox_offline_theme_color' ) ) . ';
                        color: ' . esc_attr( get_option( 'talkino_chatbox_title_color' ) ) . ';
                        border-radius: 0;
                    }

                    </style>';

				}
                
			} else { // Right position.
				if ( ( true === $this->is_agents_online && ! is_plugin_active( 'talkino-bundle/talkino-bundle.php' ) && get_option( 'talkino_global_online_status' ) === 'online' ) || ( true === $this->is_agents_online && get_option( 'talkino_global_online_status' ) === 'online' && true === $this->is_global_schedule_online ) ) {

					echo '<style>
                    .talkino-chat-btn {
                        background-color: ' . esc_attr( get_option( 'talkino_chatbox_online_theme_color' ) ) . ';
                        min-width: 230px;
                        height: 40px;
                        right: 15px;
                        bottom: 0;
                        border-radius: 5px 5px 0 0;
                    }

                    .talkino-rectangle-label {
                        color: ' . esc_attr( get_option( 'talkino_chatbox_online_icon_color' ) ) . ';
                    }

                    .dashicons.' . str_replace( ' ', '.', esc_attr( get_option( 'talkino_chatbox_icon' ) ) ) . '.round {
                        display: none;
                    }

                    .talkino-chat-wrapper {
                        background-color: ' . esc_attr( get_option( 'talkino_chatbox_background_color' ) ) . ';
                        right: 20px;
                        border-radius: 0;
                        ' . esc_attr( $wrapper_animation ) . '
                    }

                    .talkino-chat-title {
                        background-color: ' . esc_attr( get_option( 'talkino_chatbox_online_theme_color' ) ) . ';
                        color: ' . esc_attr( get_option( 'talkino_chatbox_title_color' ) ) . ';
                        border-radius: 0;
                    }

                    </style>';

				} elseif ( ( true === $this->is_agents_online && ! is_plugin_active( 'talkino-bundle/talkino-bundle.php' ) && get_option( 'talkino_global_online_status' ) === 'away' ) || ( true === $this->is_agents_online && get_option( 'talkino_global_online_status' ) === 'away' && true === $this->is_global_schedule_online ) ) {

					echo '<style>
                    .talkino-chat-btn {
                        background-color: ' . esc_attr( get_option( 'talkino_chatbox_away_theme_color' ) ) . ';
                        min-width: 230px;
                        height: 40px;
                        right: 15px;
                        bottom: 0;
                        border-radius: 5px 5px 0 0;
                    }

                    .talkino-rectangle-label {
                        color: ' . esc_attr( get_option( 'talkino_chatbox_away_icon_color' ) ) . ';
                    }

                    .dashicons.' . str_replace( ' ', '.', esc_attr( get_option( 'talkino_chatbox_icon' ) ) ) . '.round {
                        display: none;
                    }

                    .talkino-chat-wrapper {
                        background-color: ' . esc_attr( get_option( 'talkino_chatbox_background_color' ) ) . ';
                        right: 20px;
                        border-radius: 0;
                        ' . esc_attr( $wrapper_animation ) . '
                    }

                    .talkino-chat-title {
                        background-color: ' . esc_attr( get_option( 'talkino_chatbox_away_theme_color' ) ) . ';
                        color: ' . esc_attr( get_option( 'talkino_chatbox_title_color' ) ) . ';
                        border-radius: 0;
                    }

                    </style>';

				} else {

					echo '<style>
                    .talkino-chat-btn {
                        background-color: ' . esc_attr( get_option( 'talkino_chatbox_offline_theme_color' ) ) . ';
                        min-width: 230px;
                        height: 40px;
                        right: 15px;
                        bottom: 0;
                        border-radius: 5px 5px 0 0;
                    }

                    .talkino-rectangle-label {
                        color: ' . esc_attr( get_option( 'talkino_chatbox_offline_icon_color' ) ) . ';
                    }

                    .dashicons.' . str_replace( ' ', '.', esc_attr( get_option( 'talkino_chatbox_icon' ) ) ) . '.round {
                        display: none;
                    }

                    .talkino-chat-wrapper {
                        background-color: ' . esc_attr( get_option( 'talkino_chatbox_background_color' ) ) . ';
                        right: 20px;
                        border-radius: 0;
                        ' . esc_attr( $wrapper_animation ) . '
                    }

                    .talkino-chat-title {
                        background-color: ' . esc_attr( get_option( 'talkino_chatbox_offline_theme_color' ) ) . ';
                        color: ' . esc_attr( get_option( 'talkino_chatbox_title_color' ) ) . ';
                        border-radius: 0;
                    }

                    </style>';

				}
			}
		}
	}

}
