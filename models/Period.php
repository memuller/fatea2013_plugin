<?php
	namespace FateaVestibular ;  
	use CustomPost, DateTime ;

	class Period extends CustomPost {

		static $name = "period" ;
		static $editable_by = array('form_advanced');
		static $creation_fields = array( 
			'label' => 'period','description' => 'Período promocional, que antecede um sorteio.',
			'public' => true,'show_ui' => true,'show_in_menu' => true,'capability_type' => 'post',
			'hierarchical' => false,'rewrite' => array('slug' => 'programacao'),'query_var' => true,
			'supports' => array('custom-fields'), 'publicly_queryable' => false,
			'has_archive' => true, 'taxonomies' => array(),
			'labels' => array (
				'name' => 'Períodos',
				'singular_name' => 'Período',
				'menu_name' => 'Períodos',
				'add_new' => 'Novo período',
				'add_new_item' => 'Registrar período',
				'edit' => 'Alterar',
				'edit_item' => 'Alterar período',
				'new_item' => 'Registrar período',
				'view' => 'Consultar',
				'view_item' => 'Consultar período',
				'search_items' => 'Buscar períodos',
				'not_found' => 'Nenhum período cadastrado.',
				'not_found_in_trash' => 'Nenhum período foi encontrado na lixeira.'
			)
		) ;

		static $fields = array(
			'order' => array('type' => 'integer', 'hidden' => true),
			'start_date' => array('type' => 'date', 'label' => 'Data de início', 'description' => 'início do recebimento de cupons deste período.'),
			'end_date' => array('type' => 'date', 'label' => 'Data de término', 'description' => 'término do recebimento de cupons neste período.' ),
			'lottery_date' => array('type' => 'date', 'label' => 'Data do sorteio', 'description' => 'sorteio correspondente aos cupons deste período.' ),
			'num_prizes' => array('type' => 'integer', 'label' => 'Número de prêmios', 'description' => 'o número de cupons que serão efetivamente sorteados.'),
			'announced_product' => array('type' => 'product', 'label' => 'Produto anunciado', 'description' => 'item divulgado ao longo do período.' )
		) ;

		static function build(){
			parent::build(); 
			$class = get_called_class();

			add_filter('title_save_pre', function($title) use($class) {
				global $post ;
				if($class::$name == $_POST['post_type'] && $post->post_status != 'publish'){
					$period_names = array('Primeiro', 'Segundo', 'Terceiro', 'Quarto');
					$num_periods = sizeof(get_posts(array('post_type' => $class::$name)));
					$title = $period_names[$num_periods];
					$_POST[$class::$name]['order'] = $num_periods +1; 
				}
				return $title ;
			}) ;
		}

		static function now(){
			return new DateTime('now');
		}

		public function upcoming(){
			$in_time = $this->date('start_date') > static::now()  ;
			return (bool)$in_time ;
		}

		public function started(){
			$in_time = $this->date('start_date') <= static::now()  ;
			return (bool)$in_time ;	
		}

		public function expired(){
			$in_time = $this->date('end_date') < static::now() ; 
			return (bool)$in_time ;
		}

		public function done(){
			return (bool) ($this->date('lottery_date') < static::now());
		}

		public function active(){ return $this->started() && !$this->expired(); }

		static function all($params=array()){
			$params = array_merge(array(
				'post_type' => static::$name, 'meta_key' => 'order', 
				'orderby' => 'meta_value', 'order' => 'desc'), $params
			);
			$posts = array();
			foreach(get_posts($params) as $post){
				$posts[]= new static($post);
			}
			return $posts;
		}

		static function days_until_next_lottery(){
			foreach (static::all() as $period) {
				if($period->upcoming() || $period->started()) {
					return $period->days_until();
				}
			}
		}

		public function days_until($field='lottery_date'){
			$now = new DateTime('now'); $date = $this->date($field);
			$diff = $date->diff($now);
			return $diff->format('%a');
		}

		static function current(){
			$periods = get_posts(array('post_type' => static::$name));
			foreach ($periods as $period) {
				$period = new static($period);
				if($period->active()) return $period;
			}
			return null;
		}


		
	}

 ?>