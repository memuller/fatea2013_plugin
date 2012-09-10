<?php  
	namespace FateaVestibular\Presenters ;
	use Presenter ; 

	class Course extends Presenter {

		static $uses = array('styles', 'scripts');
		static $presents_for = array('custom_post' => 'course');
		
		static function scripts(){
			if(is_post_type_archive('course')){
				wp_enqueue_script('jquery');
				wp_enqueue_script('jquery-caroufredsel', static::url('js/jquery-caroufredsel/jquery.carouFredSel-5.6.4-packed.js'), array('jquery') );
				wp_enqueue_script('course', static::url('js/course.js'));
			}

		}

	}
?>