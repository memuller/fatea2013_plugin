<?php  
	namespace FateaVestibular\Presenters ;
	use Presenter ; 

	class Course extends Presenter {

		static $uses = array('styles', 'scripts', 'admin_scripts');
		static $presents_for = array('custom_post' => 'course');
		
		static function build(){
			parent::build();
			$class = get_called_class(); $namespace = get_namespace($class); 

			add_action('pre_get_posts', function($query) use($class, $namespace) {
				if( !is_admin() && $query->is_main_query() && $query->query['post_type'] == 'course' ){
					$query->set('order', 'ASC'); $query->set('orderby', 'title');
				}
			});
		}

		static function scripts(){
			if(is_post_type_archive('course')){
				wp_enqueue_script('jquery');
				wp_enqueue_script('jquery-caroufredsel', static::url('js/jquery-caroufredsel/jquery.carouFredSel-5.6.4-packed.js'), array('jquery') );
				wp_enqueue_script('course', static::url('js/course.js'));
			}

		}

	}
?>