<?php  
	namespace FateaVestibular\Presenters ;
	use Presenter ; 

	class Base extends Presenter {
		static function build(){
			$class = get_called_class();
			add_action('wp_enqueue_scripts', function() use($class) {
				wp_enqueue_script('jquery-colorbox', $class::url('js/jquery-colorbox/jquery.colorbox-min.js'), array('jquery'));
				wp_enqueue_script('main', $class::url('js/main.js'), array('jquery', 'jquery-colorbox'));
			});
		}
	}
?>