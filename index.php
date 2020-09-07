<?php
/*
Plugin Name: درگاه پرداخت زیبال برای افزونه فروش پست
Version: 1.0
Description:  درگاه پرداخت واسط زیبال برای افزونه فروش پست ها post shop
Plugin URI: http://zibal.ir
Author: Yahya Kangi
Author URI: http://github.com/YahyaKng/
License: GPL3
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function ps_load_zibal_payment() {
	function ps_add_zibal_payment( $list ) {
		$list['zibal'] = array(
			'name'       => 'زیبال',
			'class_name' => 'ps_zibal',
			'settings'   => array(
				'merchant_id' => array( 'name' => 'کد درگاه (مرچنت) زیبال' )
			)
		);

		return $list;
	}

	function ps_load_zibal_class() {
		include_once plugin_dir_path( __FILE__ ) . '/ps_zibal.php';
	}

	if ( class_exists( 'ps_payment_gateway' ) && ! class_exists('ps_sms_newsms') ) {
		add_filter( 'ps_payment_list', 'ps_add_zibal_payment' );
		add_action( 'ps_load_payment_class', 'ps_load_zibal_class' );
	}
}

add_action( 'plugins_loaded', 'ps_load_zibal_payment', 0 );


add_action( 'admin_notices', 'ps_zibal_check_requirement' );

function ps_zibal_check_requirement() {
	if ( current_user_can( 'activate_plugins' ) ) {
		if ( ! class_exists( 'ps_payment_gateway' ) ) {
			echo '<div class="notice notice-warning is-dismissible">';
			echo 'برای استفاده از این درگاه پرداخت نیاز به افزونه فروش پست ها است،لطفا این پلاگین رو خریداری, نصب و فعال نمایید.';
			echo '<br><a href="http://iwebpro.ir">اطلاعات بیشتر ...</a>';
			echo '</div>';
		} elseif ( version_compare( PS_VERSION, '5.5.0', '<' ) ) {
			echo '<div class="notice notice-warning is-dismissible">';
			echo 'برای استفاده از این پلاگین ورژن افزونه فروش پست ها باید حداقل 5.5 باشد!';
			echo '<br><a href="http://iwebpro.ir">اطلاعات بیشتر ...</a>';
			echo '</div>';
		}
	}
}