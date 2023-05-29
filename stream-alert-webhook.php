<?php
/**
 * Plugin Name: Stream Alert Webhook
 * Description: Extends XWP Stream to add a webhook as a alert type
 * Version: 1.0
 * Author: Tim Nash
 * Author URI: https://timnash.co.uk
 * Text Domain: tn-stream-alert-webhook
 * Domain Path: /languages
 * License: GPLv2
 * 
 * @package tn-stream-alert-webhook
 */

 /**
  * Copyright (c) Tim Nash
  * This program is free software; you can redistribute it and/or modify
  * it under the terms of the GNU General Public License, version 2 or, at
  * your discretion, any later version, as published by the Free
  * Software Foundation.
  *
  * This program is distributed in the hope that it will be useful,
  * but WITHOUT ANY WARRANTY; without even the implied warranty of
  * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  * GNU General Public License for more details.
  *
  * You should have received a copy of the GNU General Public License
  * along with this program; if not, write to the Free Software
  * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
  */


 /**
  * Add the webhook alert type to the filter
  * @param array $classes
  * @return array
  */
 function tn_stream_alert_type_webhook_add( $classes ){   
    if( empty( $GLOBALS['wp_stream'] ) || is_null( $GLOBALS['wp_stream'] ) ){
        return;
    }
    require_once __DIR__ . '/alerts/class-alert-type-webhook.php';
    $classes[ 'webhook' ] = new \WP_Stream\Alert_Type_Webhook( $GLOBALS['wp_stream'] );
    return $classes;
 }
 add_filter( 'wp_stream_alert_types', 'tn_stream_alert_type_webhook_add' );