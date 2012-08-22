<?php
	namespace FateaVestibular ;  

	class Plugin {

		static $name = 'FateaVestibular' ;
		static $db_version = 0.0 ;
		static $custom_posts = array('Course' );
		static $custom_classes = array();
		static $restricted_menus = array();

		static function path($path){
			return plugin_dir_path(dirname(__FILE__)). $path;
		}

		static function url($url){
			return plugin_dir_url(dirname(__FILE__)). $url ;
		}

		static function enforce_db(){
			$db_version = get_option( static::$name . '_db_version', '0');
			if( floatval($db_version) < static::$db_version ) {	
				foreach (static::$custom_classes as $class) {
					$class =  '\\'.static::$name.'\\'.$class;
					$class::build_database();
				}
				update_option( static::name . '_db_version', static::$db_version );
			}
			
		}

		static function build(){
			$base = get_called_class();
			foreach (array_merge(static::$custom_classes, static::$custom_posts) as $object) {
				require( static::path('models/'. $object . '.php'));
				$class = static::$name.'\\'. ucfirst($object);
				$class::build();

			}
			require static::path('presenters/Base.php');

			add_action('plugins_loaded', static::enforce_db() ) ;

			if(!empty(static::$restricted_menus)){
				$restricted_menus = static::$restricted_menus;
				add_action('admin_menu', function() use ($restricted_menus){
					global $menu ; $restricted = array();
					foreach ($restricted_menus as $item) {
						$restricted[]= __($item);
					}
					end ($menu);
					while (prev($menu)){
						$value = explode(' ',$menu[key($menu)][0]);
						if(in_array($value[0] != NULL?$value[0]:"" , $restricted)){unset($menu[key($menu)]);}
					}
				} );
			}

			add_action('save_post', function($post_id) use($base) {
				$post_type = $_POST['post_type'];
				if( defined(DOING_AUTOSAVE) && DOING_AUTOSAVE) return ;
				if( ! in_array(ucfirst($post_type), $base::$custom_posts )) return ;
				$object = $_POST[$post_type]; $class = '\\'.$base::$name.'\\'. ucfirst($post_type);
				foreach ($object as $field_name => $field_value) {
					if(isset($class::$fields[$field_name]) ){
						update_post_meta($post_id, $field_name, $field_value) ;
					}
				}
			});

			add_action('admin_enqueue_scripts', function() use ($base) {
				wp_enqueue_style(__NAMESPACE__.'-admin', $base::url('css/admin/main.css') );
				$screen = get_current_screen() ;
				if( in_array(ucfirst($screen->post_type), $base::$custom_posts )){
					wp_enqueue_script( $screen->post_type, $base::url( "js/admin/$screen->post_type.js") );
					wp_enqueue_style( $screen->post_type, $base::url( "css/admin/$screen->post_type.css") );
					$class = get_namespace($base).'\\'.ucfirst($screen->post_type);
					
					foreach ($class::$fields as $field => $options) {
						if( 'date' == $options['type'] ){
							wp_enqueue_script('jquery-datepick', $base::url('js/jquery-datepick/jquery.datepick.js'), array('jquery'));
							wp_enqueue_script('jquery-datepick-br', $base::url('js/jquery-datepick/jquery.datepick-pt-BR.js'), array('jquery-datepick'));
							wp_enqueue_style('jquery-datepick', $base::url('js/jquery-datepick/smoothness.datepick.css'));
						}
					}
				}
			});
		}

	}

 ?>