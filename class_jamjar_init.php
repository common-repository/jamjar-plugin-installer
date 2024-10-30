<?php
if ( ! defined( 'ABSPATH' ) )
		exit; // Exit if accessed directly
require_once(ABSPATH .'/wp-admin/includes/plugin.php');
if ( ! class_exists( 'JamjarPluginInstaller' ) ) {

	class JamjarPluginInstaller{
		public $admininstance;

		function __construct($admininstance){
			$this->init();
			$this->initScripts();
			$this->admininstance = $admininstance;
		}

		public function addMenuToDashboard(){
			    add_plugins_page(
			        __( 'Jamjar Plugin Installer', 'plugin-monkey' ),
			        'Jamjar Plugin Installer',
			        'manage_options',
			        'plugin-monkey-manager',
			        array(&$this,'pluginPage'),
			        plugins_url( 'myplugin/images/icon.png' ),
			        21
			    );


		}

		public function addTabToPluginPage($tabs){
			$tabs["jamjar"] = 'Jamjar Plugin Installer';
			return $tabs;
		}

		public function showBanner(){
	        $logo = plugins_url("/assets/images/jamjar-256x256.png",__FILE__);
	        $like = '<iframe src="https://www.facebook.com/plugins/like.php?href=https%3A%2F%2Fwww.facebook.com%2Fgalactas%2F&width=51&layout=button&action=like&size=small&show_faces=false&share=false&height=65&appId" width="51" height="20" style="border:none;overflow:hidden;float: inherit;margin-left: 7px;" scrolling="no" frameborder="0" allowTransparency="true" allow="encrypted-media"></iframe>';
	        $banner = "<div class='jamjar-banner-wrapper'><img src='{$logo}' /><h3>Jamjar Plugin Installer{$like}</h3><p>This plugin developed and maintained by <a href='https://www.galactas.com'>Galactas (M) Sdn Bhd/Pte Ltd</a>.</br>If you like this plugin,please support us by liking our Facebook page <a href='https://www.facebook.com/galactas'>here</a> or by simply clicking the like button above.It will encourage us to create more free plugins for you.</a></p></p></div>";
	        echo $banner;
        
    	}

		public function pluginPage(){
			include_once("view/admin_dashboard_view.php");
		}

		public function init(){
			add_action( 'admin_menu', array($this,'addMenuToDashboard') );
			add_filter( 'install_plugins_nonmenu_tabs', array( $this, 'addTabToPluginPage' ) );
			add_filter( 'install_plugins_tabs', array( $this, 'addTabToPluginPage' ) );
			add_action( 'install_plugins_jamjar', array( $this, 'pluginPage' ) );

		}

		

		public function initScripts(){
			$params = array ( 'ajaxurl' => admin_url( 'admin-ajax.php' ),'plugins_url' => plugins_url(__FILE__));
			wp_enqueue_script("jj-simple-upload",plugins_url("/assets/js/simpleUpload.js",__FILE__),array("jquery"),false,true);
			wp_enqueue_script("jj-admin-js",plugins_url("/assets/js/jj-admin.js",__FILE__),array("jquery"),false,true);
			wp_enqueue_script("jj-dropzone-js",plugins_url("/assets/js/dropzone/dropzone.js",__FILE__),array("jquery"),false,true);
			wp_localize_script( 'jj-admin-js', 'params', $params );		

			wp_enqueue_style("jj-style",plugins_url("/assets/css/jj-style.css",__FILE__));
			wp_enqueue_style("jj-dropzone",plugins_url("/assets/js/dropzone/dropzone.css",__FILE__));

		}


	


	}


	new JamjarPluginInstaller($admininstance);
}
