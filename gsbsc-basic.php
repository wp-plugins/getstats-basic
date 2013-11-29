<?php
/*
	Plugin Name: getStats: Basic
	Plugin URI: www.getstatr.com
	Description: Know your visitors better: Post content that is relevant to the geo location of your audience based on the getStats stats plugin! 
	Version: 1.0
	Author: Jason Bailey
	Author URI: www.getstatr.com
	License: GPL2
*/

	function gsbscstat_modify_menu(){
		add_menu_page( 'getStats Basic', 'Visitor stat', 'manage_options', 'gsbsc-stat', 'gsbscstat_options' );
	}
	
	add_action('admin_menu','gsbscstat_modify_menu');

	function gsbscstat_options(){
		include('gsbsc-admin.php');
	}
	
	define('gsbscstat_url',WP_PLUGIN_URL."/getstats-basic/");
	
	register_activation_hook(WP_PLUGIN_DIR.'/getstats-basic/gsbsc-basic.php','set_getstatsbsc_options');
	
	function set_getstatsbsc_options(){
		global $wpdb;
		$ins_q = "CREATE TABLE IF NOT EXISTS ".$wpdb->prefix."bsc_stat (		
			`id` int(11) NOT NULL AUTO_INCREMENT,
  			`ip` varchar(100) NOT NULL,
  			`referrer` varchar(5000) NOT NULL,
  			`ua` varchar(5000) NOT NULL,
  			`page` varchar(5000) NOT NULL,
  			`vcount` int(10) NOT NULL,
  			`vtime` datetime NOT NULL,
  			PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";
		$wpdb->query($ins_q);
	}
	
	function gsbscstat_enqueue() {	
		wp_enqueue_script( 'gsbscstat-js-script', gsbscstat_url . 'gsbscstat.script.js', array( 'jquery' ));
		wp_localize_script( 'gsbscstat-js-script', 'gsbscstatajx', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ),'checkReq' => wp_create_nonce( 'bsctatauthrequst58qa' )));
	}
	add_action( 'wp_enqueue_scripts', 'gsbscstat_enqueue' );
	
	add_action( "wp_ajax_gsbscstat_add", "gsbscstat_add" );
	add_action( "wp_ajax_nopriv_gsbscstat_add", "gsbscstat_add" );
	
	
	function gsbscstat_add(){
		
		if(!isset($_POST['checkReq']) || !wp_verify_nonce( $_POST['checkReq'], 'bsctatauthrequst58qa' )){
			exit;
		}
		$page = esc_url($_POST['path']);
		
		global $wpdb;
	
		$u_ip = $_SERVER['REMOTE_ADDR'];
		$u_ua = $_SERVER['HTTP_USER_AGENT'];
		$referrer = $_SERVER['HTTP_REFERER'];
		
		$check = $wpdb->get_results("select id from ".$wpdb->prefix."bsc_stat where ip = '".$u_ip."' and page = '".$page."'");
		if($check){
			$query = "update ".$wpdb->prefix."bsc_stat set vcount = vcount+1,vtime = now() where ip = '".$u_ip."' and page = '".$page."'";
			$wpdb->query($query);
		}
		else
		{
			$query = "insert into ".$wpdb->prefix."bsc_stat (ip,referrer,ua,page,vcount,vtime) values('".$u_ip."','".$referrer."','".$u_ua."','".$page."',1,now())";
			$wpdb->query($query);
		}
		exit;
	}