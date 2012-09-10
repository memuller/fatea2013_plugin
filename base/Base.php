<?php
	namespace FateaVestibular ;  

	class Plugin {

		static $name = 'FateaVestibular' ;
		static $db_version = 0.0 ;
		static $custom_posts = array('Course' );
		static $custom_classes = array();
		static $custom_singles = array();
		static $presenters = array('Course');
		static $restricted_menus = array();
		static $roles = array(

		);


		static $absent_roles = array('author', 'collaborator');

		static function path($path){
			return plugin_dir_path(dirname(__FILE__)). $path;
		}

		static function url($url){
			return plugin_dir_url(dirname(__FILE__)). $url ;
		}

		static function build(){
			$base = get_called_class(); $namespace = '\\'.get_namespace($base) . '\\';
			foreach (array_merge(static::$custom_classes, static::$custom_posts, static::$custom_singles) as $object) {
				require( static::path('models/'. $object . '.php'));
				$class = $namespace. ucfirst($object);
				$class::build();

			}

			array_push(static::$presenters, 'Base');
			foreach (static::$presenters as $presenter) {
				require static::path("presenters/$presenter.php");
				$presenter = $namespace.'Presenters\\'.$presenter;
				$presenter::build();
			}

			add_action('plugins_loaded', function() use($base, $namespace) {
				$prefix = strtolower(str_replace('\\', '', $namespace));
				$db_version = get_option( $prefix.'_db_version', '0');
				if( floatval($db_version) < $base::$db_version) {	
					foreach ($base::$custom_classes as $class) {
						$class = $namespace. $class ;
						$class::build_database();
					}

					$all_capabilities = array();
					foreach ($base::$roles as $role => $options) {
						remove_role($role);
						$inherits_from = get_role( isset($options['inherits_from']) ? $options['inherits_from'] : 'editor' ); 
						if(isset($options['can'])){
							$capabilities = array_merge($inherits_from->capabilities, $options['can']); 
							$all_capabilities = array_merge($all_capabilities, $capabilities);
							
						} else { $capabilities = $inherits_from->capabilities ; }
						add_role($role, $options['label'], $capabilities );


					}

					$admin = get_role('administrator');
					if(!empty($all_capabilities)){
						foreach ($all_capabilities as $capability) {
							$admin->add_cap($capability);		
						}
					}

					update_option( 'casanova_db_version', $base::$db_version );
				}
			} );

			if(!empty(static::$restricted_menus)){
				$restricted_menus = static::$restricted_menus;
				add_action('admin_menu', function() use ($restricted_menus){
					if(!current_user_can('manage_options')){
						global $menu ; $restricted = array();
						foreach ($restricted_menus as $item) {
							$restricted[]= __($item);
						}
						end ($menu);
						while (prev($menu)){
							$value = explode(' ',$menu[key($menu)][0]);
							if(in_array($value[0] != NULL?$value[0]:"" , $restricted)){unset($menu[key($menu)]);}
						}
					}
				} );
			}

			add_action('save_post', function($post_id) use($base, $namespace) {
				
				if( defined(DOING_AUTOSAVE) && DOING_AUTOSAVE) return ;

				if(isset($_POST['custom_single']) && in_array($_POST['custom_single'], $base::$custom_singles) ){
					$class = $namespace.$_POST['custom_single']; $object = $_POST[$_POST['custom_single']];
				}

				if( in_array(ucfirst($_POST['post_type']), $base::$custom_posts )){
					$object = $_POST[$_POST['post_type']]; $class = $namespace. ucfirst($_POST['post_type']);	
				}

				if(!isset($object)) return ;
				
				foreach ($object as $field_name => $field_value) {
					if(isset($class::$fields[$field_name]) ){
						update_post_meta($post_id, $field_name, $field_value) ;
					}
				}
			});

			add_action('admin_enqueue_scripts', function() use ($base, $namespace) {
				wp_enqueue_style(__NAMESPACE__.'-admin', $base::url('css/admin/main.css') );
				$screen = get_current_screen() ;
				if( in_array(ucfirst($screen->post_type), $base::$custom_posts )){
					wp_enqueue_script( $screen->post_type, $base::url( "js/admin/$screen->post_type.js") );
					wp_enqueue_style( $screen->post_type, $base::url( "css/admin/$screen->post_type.css") );
					$class = $namespace.ucfirst($screen->post_type);
					
					foreach ($class::$fields as $field => $options) {
						if( 'date' == $options['type'] ){
							wp_enqueue_script('jquery-datepick', $base::url('js/jquery-datepick/jquery.datepick.js'), array('jquery'));
							wp_enqueue_script('jquery-datepick-br', $base::url('js/jquery-datepick/jquery.datepick-pt-BR.js'), array('jquery-datepick'));
							wp_enqueue_style('jquery-datepick', $base::url('js/jquery-datepick/smoothness.datepick.css'));
						}
						if('image' == $options['type']){
							wp_enqueue_script('media_upload');
							wp_enqueue_script('thickbox');
							add_action('admin_print_scripts', function() use($base, $namespace, $class, $field) {
								$script = sprintf("jQuery(document).ready( function($) {
									$('#post').on('click', '.image-upload', function(){
										window.image_uploader_field = '#%s' ;
										tb_show('', 'media-upload.php?type=image&TB_iframe=true') ;
										return false;
									});
									window.send_to_editor = function(html){
										$(window.image_uploader_field).val( $(html)[0]) ;
										tb_remove();
									}
								});",$class::$name.'_'.$field ) ;
								print '<script>'.$script.'</script>';
							}, 99);
						}
					}
				}
			});

			if(!empty(static::$roles)){
				add_action('current_screen', function() use($base) {
					global $current_user ;
					if(isset($base::$roles[$current_user->roles[0]])){
						if(isset($base::$roles[$current_user->roles[0]]['landing_page']) ){
							$screen = get_current_screen();
							if($screen->id == 'dashboard' ) wp_redirect(admin_url( $base::$roles[$current_user->roles[0]]['landing_page'] ));
						}
						if(isset($base::$roles[$current_user->roles[0]]['collapse_menu'])){
							add_action('admin_enqueue_scripts', function() use($base) {
								wp_enqueue_script('collapse-menu', $base::url('js/admin/utils/collapse_menu.js'), array('jquery'));
							});
						}
					}
				});

			}

			if(!empty(static::$absent_roles) || false ){
				add_action('admin_init', function() use ($base, $namespace){
					foreach ($base::$absent_roles as $role) {
						remove_role($role);
					}
				});
			}

		}

	}

 ?>