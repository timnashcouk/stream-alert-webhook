<?php

/**
 * Webhook Alerts.
 *
 * @package WP_Stream
 */

namespace WP_Stream;

/**
 * Class Alert_Type_Webhook
 *
 * @package WP_Stream
 */
class Alert_Type_Webhook extends Alert_Type {

	/**
	 * Alert type name
	 *
	 * @var string
	 */
	public $name = 'Webhook';

	/**
	 * Alert type slug
	 *
	 * @var string
	 */
	public $slug = 'webhook';

	/**
	 * Class Constructor
	 *
	 * @param Plugin $plugin Plugin object.
	 */
	public function __construct( $plugin ) {
		parent::__construct( $plugin );
		$this->plugin = $plugin;
		if ( ! is_admin() ) {
			return;
		}
		add_filter(
			'wp_stream_alerts_save_meta',
			array(
				$this,
				'add_alert_meta',
			),
			10,
			2
		);
	}

	/**
	 * Sends a HTTP Request to an external endpoint.
	 *
	 * @param int   $record_id Record that triggered notification.
	 * @param array $recordarr Record details.
	 * @param Alert $alert Alert options.
	 * @return void
	 */
	public function alert( $record_id, $recordarr, $alert ) {
		$options = wp_parse_args(
			$alert->alert_meta,
			array(
				'webhook'           => '',
				'trigger_action'    => '',
				'trigger_connector' => '',
				'trigger_context'   => '',
			)
		);
		if ( empty( $options['webhook'] ) ) {
			return;
		}
		$user_id = (int) $recordarr['user_id'];
		$user    = get_userdata( $user_id );
		$logo    = wp_get_attachment_image_src( get_theme_mod( 'custom_logo' ), 'full' );
		$context = $recordarr['context'];
		$action  = $recordarr['action'];

		if ( ! empty( $alert->alert_meta['trigger_context'] ) ) {
			$context = $this->plugin->alerts->alert_triggers['context']->get_display_value( 'list_table', $alert );
		}
		if ( ! empty( $alert->alert_meta['trigger_action'] ) ) {
			$action = $this->plugin->alerts->alert_triggers['action']->get_display_value( 'list_table', $alert );
		}

		$fields = array(
			array(
				'title' => 'IP Address',
				'value' => $recordarr['ip'],
				'short' => true,
			),
			array(
				'title' => 'Connector',
				'value' => $recordarr['connector'],
				'short' => true,
			),
			array(
				'title' => 'Context',
				'value' => $context,
				'short' => true,
			),
			array(
				'title' => 'Action',
				'value' => $action,
				'short' => true,
			),
		);

		$post = null;
		if ( isset( $recordarr['object_id'] ) ) {
			$post_id = $recordarr['object_id'];
			$post    = get_post( $post_id );
		}
		if ( is_object( $post ) && ! empty( $post ) ) {
			$post_type      = get_post_type_object( $post->post_type );
			$edit_post_link = get_edit_post_link( $post->ID, 'raw' );
			array_push(
				$fields,
				array(
					'title' => 'Edit ' . $post_type->labels->singular_name,
					'value' => "<$edit_post_link>",
					'short' => false,
				)
			);
		}

		$edit_alert_link = admin_url( 'edit.php?post_type=wp_stream_alerts#post-' . $alert->ID );
		array_push(
			$fields,
			array(
				'title' => 'Edit Alert',
				'value' => "<$edit_alert_link>",
				'short' => false,
			)
		);

		$data = array(
			'author_icon' => get_avatar_url( $user_id, 16 ),
			'author_link' => admin_url( "admin.php?page=wp_stream&user_id=$user_id" ),
			'author_name' => trim( "$user->first_name $user->last_name" ),
			'fallback'    => html_entity_decode( $recordarr['summary'], ENT_COMPAT ),
			'fields'      => $fields,
			'site_name'   => get_bloginfo( 'name' ),
            'site_url'    => get_bloginfo( 'url' ),
			'site_icon'   => get_site_icon_url( 16, $logo[0], $recordarr['blog_id'] ),
			'title'       => html_entity_decode( $recordarr['summary'], ENT_COMPAT ),
			'ts'          => strtotime( $recordarr['created'] ),
		);
		if ( array_key_exists( 'object_id', $recordarr ) ) {
			$object_id                = (int) $recordarr['object_id'];
			$context                  = $recordarr['context'];
			$data['title_link'] = admin_url( "admin.php?page=wp_stream&object_id=$object_id&context=$context" );
		}
	
		
		wp_remote_post(
			$options['webhook'],
			array(
				'body'    => wp_json_encode( $data ),
				'headers' => array( 'Content-Type' => 'application/json' ),
			)
		);
	}

	/**
	 * Displays a settings form for the alert type
	 *
	 * @param Alert $alert Alert object for the currently displayed alert.
	 * @return void
	 */
	public function display_fields( $alert ) {
		$alert_meta = array();
		if ( is_object( $alert ) ) {
			$alert_meta = $alert->alert_meta;
		}
		$options = wp_parse_args(
			$alert_meta,
			array(
				'webhook'  => '',
			)
		);
		$form    = new Form_Generator();
		echo '<span class="wp_stream_alert_type_description">' . esc_html__( 'Send a JSON notification to endpoint.', ' tn-stream-alert-webhook' ) . '</span>';
		echo '<label for="wp_stream_slack_webhook"><span class="title">' . esc_html__( 'Webhook URL', ' tn-stream-alert-webhook' ) . '</span>';
		echo '<span class="input-text-wrap">';
		echo $form->render_field(
			'text',
			array(
				'name'  => 'wp_stream_webhook',
				'title' => esc_attr( __( 'Webhook URL', ' tn-stream-alert-webhook' ) ),
				'value' => $options['webhook'],
			)
		); // Xss ok.
		echo '</span>';
		echo '<span class="input-text-wrap">' . esc_html__( 'The webhook URL', ' tn-stream-alert-webhook' ) . '</span>';
		echo '</label>';
	}

	/**
	 * Validates and saves form settings for later use.
	 *
	 * @param Alert $alert Alert object for the currently displayed alert.
	 * @return void
	 */
	public function save_fields( $alert ) {
		check_admin_referer( 'save_alert', 'wp_stream_alerts_nonce' );

		$webhook = wp_stream_filter_input( 'INPUT_POST', 'wp_stream_webhook', 'FILTER_VALIDATE_URL' );
		if ( ! empty( $webhook ) ) {
			$alert->alert_meta['webhook'] = $webhook;
		}
	}

	/**
	 * Add alert meta if this is a Slack alert
	 *
	 * @param array  $alert_meta The metadata to be inserted for this alert.
	 * @param string $alert_type The type of alert being added or updated.
	 *
	 * @return mixed
	 */
	public function add_alert_meta( $alert_meta, $alert_type ) {
		if ( $this->slug === $alert_type ) {
			$webhook = wp_stream_filter_input( INPUT_POST, 'wp_stream_webhook' );
			if ( ! empty( $webhook ) ) {
				$alert_meta['webhook'] = $webhook;
			}
		}

		return $alert_meta;
	}
}