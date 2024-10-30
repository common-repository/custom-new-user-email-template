<?php
//Options page

/**
 * Plugin options markup
 */
function cnut_plugin_options() {

	// get variables
	
	$blogname 	 = get_option('blogname');
	$admin_email = get_option('admin_email');
	
	$email_info = cnut_get_email_info();

	// output content
	
	echo'<div class="wrap cnut-html-notification-wrap">';
		
		echo'<h2>' . __( 'Custom User Notification', 'custom-new-user-notification' ) . '</h2>';
		
		echo'<h4>' . __( 'Shortcode\'s', 'custom-new-user-notification' ) . '</h4>';
		
		echo'<ul>';
			echo'<li class="cnut-shortcode">User name 	: [cnut-display-name]</li>';
			echo'<li class="cnut-shortcode">User login 	: [cnut-user-login]</li>';
			//echo'<li class="cnut-shortcode">Password 	: [cnut-user-password]</li>';
			echo'<li class="cnut-shortcode">User email 	: [cnut-user-email]</li>';
			echo'<li class="cnut-shortcode">Password url: [cnut-reset-password-url]</li>';
		echo'</ul>';
		
		echo'<hr />';
		
		echo'<form method="post" action="options.php">';

			settings_fields( 'cnut-settings-group' );
			do_settings_sections( 'cnut-settings-group' );

			echo'<table class="form-table">';
			
				// user email setup
			
				echo'<tr valign="top">';

					echo'<th scope="row">' . __( 'User Mail Subject', 'custom-new-user-notification' ) . '</th>';
					echo'<td><input class="cnut-mail-subject" type="text" name="cnut_user_mail_subject" value="' .  $email_info['subject_user'] . '" /></td>';

				echo'</tr>';
				echo'<tr valign="top">';

					echo'<th scope="row">' . __( 'User From Name', 'custom-new-user-notification' ) . '</th>';
					echo'<td><input class="cnut-mail-sender" type="text" name="cnut_user_mail_sender_name" placeholder="yourname" value="' .  $email_info['from_name_user'] . '" /></td>';

				echo'</tr>';
				echo'<tr valign="top">';

					echo'<th scope="row">' . __( 'User From Email', 'custom-new-user-notification' ) . '</th>';
					echo'<td>';
						echo'<input class="cnut-mail-sender" type="text" name="cnut_user_mail_sender_mail" placeholder="wordpress@yoursite.com" value="' .  $email_info['from_email_user'] . '" />';
					echo'</td>';

				echo'</tr>';			
			
				echo'<tr valign="top">';

					echo'<th scope="row">' . __( 'User Mail Content', 'custom-new-user-notification' ) . '</th>';
					echo'<td>';
					
					wp_editor( $email_info['user_mail_content'], 'cnut_user_mail_content', '' );
					
					echo'</td>';

				echo'</tr>';

				// separator
				
				echo'<tr class="cnut-sepeartion" valign="top" ></tr>';
				
				// admin email setup
				
				echo'<tr valign="top">';

					echo'<th scope="row">' . __( 'Admin Mail Subject', 'custom-new-user-notification' ) . '</th>';
					echo'<td><input class="cnut-mail-subject" type="text" name="cnut_admin_mail_subject" value="' .  $email_info['subject_admin'] . '" /></td>';

				echo'</tr>';
				echo'<tr valign="top">';

					echo'<th scope="row">' . __( 'Admin From Name', 'custom-new-user-notification' ) . '</th>';
					echo'<td><input class="cnut-mail-sender" type="text" name="cnut_admin_mail_sender_name" placeholder="yourname" value="' .  $email_info['from_name_admin'] . '" /></td>';

				echo'</tr>';
				echo'<tr valign="top">';

					echo'<th scope="row">' . __( 'Admin From Email', 'custom-new-user-notification' ) . '</th>';
					echo'<td>';
						echo'<input class="cnut-mail-sender" type="text" name="cnut_admin_mail_sender_mail" placeholder="wordpress@yoursite.com" value="' .  $email_info['from_email_admin'] . '" />';
					echo'</td>';

				echo'</tr>';				
				
				echo'<tr valign="top">';

					echo'<th scope="row">' . __( 'Admin Mail Content', 'custom-new-user-notification' ) . '</th>';
					echo'<td>';
					
					wp_editor( $email_info['admin_mail_content'], 'cnut_admin_mail_content', '' );
					
					echo'</td>';

				echo'</tr>';
				
			echo'</table>';

			submit_button();
			 
		echo'</form>';

	echo'</div>';
}

//call register settings function
add_action( 'admin_init', 'cnut_register_mysettings' );

function cnut_register_mysettings() {
	
	//register our settings
	
	register_setting( 'cnut-settings-group', 'cnut_user_mail_content' );
	register_setting( 'cnut-settings-group', 'cnut_admin_mail_content' );
	
	register_setting( 'cnut-settings-group', 'cnut_user_mail_subject' );
	register_setting( 'cnut-settings-group', 'cnut_admin_mail_subject' );
	
	register_setting( 'cnut-settings-group', 'cnut_user_mail_sender_mail' );
	register_setting( 'cnut-settings-group', 'cnut_admin_mail_sender_mail' );
	
	register_setting( 'cnut-settings-group', 'cnut_user_mail_sender_name' );
	register_setting( 'cnut-settings-group', 'cnut_admin_mail_sender_name' );
}
