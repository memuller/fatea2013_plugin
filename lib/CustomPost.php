<?php

	class CustomPost {

		static $name ;
		static $creation_fields ;
		static $fields = array() ;
		static $taxonomies = array();
		static $editable_by = array();
		public $post ; 

		function __get($name){
			global $post ;

			if(strstr($name, '-')){
				list($name, $attribute) = explode('-', $name) ;
			}

			if(isset($this->unfiltered_fields[$name])){
				return isset($attribute) ? property_or_key($this->apply_filters($name), $attribute) : $this->apply_filters($name) ;

			}

			if(in_array($name, static::$taxonomies)){
				return $this->get_term_attributes($name, $attribute) ;
			}

			if(isset(static::$fields[$name])) {
				$this->unfiltered_fields[$name] = get_post_meta($post->ID, $name, true) ;
				return $this->apply_filters($name) ;
			} else {
				return isset($this->post) ? $this->post->$name : $post->$name ; 
			}
		}

		function __construct($post=false){
			$this->unfiltered_fields = array();
			if($post){
				if(is_numeric($post)){
					$post = get_post($post) ;
				}
			} else {
				$post = $GLOBALS['post'] ;
			}
			$this->post = $post ;
			
			foreach(get_post_custom($post->ID) as $field_name => $field_values){
				if(isset(static::$fields[$field_name])){
					$this->unfiltered_fields[$field_name] = $field_values[0] ;
				}
			}
			
		}

		function apply_filters($field){
			switch (static::$fields[$field]['type']) {
				case 'array':
					return maybe_unserialize($this->unfiltered_fields[$field]) ;
					break;
				case 'integer':
					return intval($this->unfiltered_fields[$field]) ;
					break;
				default:
					return $this->unfiltered_fields[$field] ; 
					break;
			}
		}

		function get_term_attributes($taxonomy, $attribute='name'){
			if(empty($attribute)) $attribute = 'name' ;
			$terms = wp_get_object_terms($this->ID, $taxonomy) ;
			if(is_array($terms)){
				$returnable = array();
				foreach ($terms as $term) {
					$returnable[]= $term->$attribute ;
				}
				return implode(',' , $returnable) ;
			} else {
				return $terms->$attribute ;
			}
		}

		function date($field){
			if(static::$fields[$field]['type'] == 'date'){
				$date = explode('/', $this->$field) ;
				$date = sprintf("%s-%s-%s", $date[2], $date[1], $date[0]);
				return new DateTime($date) ;
			}
		}
		
		static function create_post_type(){

			register_post_type( static::$name, static::$creation_fields ) ;
		}

		static function build(){
			$class = get_called_class();
			add_action('init', $class.'::create_post_type' ) ;

			if(in_array( 'main_metabox', static::$editable_by)){
				add_action('add_meta_boxes', function() use ($class) { 
					add_meta_box($class::$name.'-main', 'Informações do '. $class::$creation_fields['labels']['singular_name'] , function() use ($class) {
						$object = new $class(); $presenter = get_namespace($class).'\Presenters\Base'; 
						$presenter::render('admin/defaults/metabox', array( 'type' => $class::$name, 'object' => $object, 'fields' => $class::$fields ));
					}, $class::$name, 'normal', 'high');
				});
			}
			if(in_array( 'form_advanced', static::$editable_by )){
				add_action('edit_form_advanced', function() use($class) {
					$screen = get_current_screen() ; 
					if($screen->post_type == $class::$name){
						$object = new $class(); $presenter = get_namespace($class).'\Presenters\Base';
						$presenter::render('admin/defaults/metabox', array( 'type' => $class::$name, 'object' => $object, 'fields' => $class::$fields ));
					}
				});
			}

		}
		
	}

 ?>