<?php
	namespace FateaVestibular ;  
	use CustomPost, DateTime ;

	class Course extends CustomPost {

		static $name = "course" ;
		static $editable_by = array(
			'form_advanced' => array( 'video_url', 'profession', 'about', 'labs', 'projects'),
			'info' => array('name' => 'Informações', 'fields' => array('periodicy', 'spaces', 'period', 'status', 'duration' ))
			 );
		static $creation_fields = array( 
			'label' => 'course','description' => '',
			'public' => true,'show_ui' => true,'show_in_menu' => true,'capability_type' => 'post',
			'hierarchical' => false,'rewrite' => array('slug' => 'cursos'),'query_var' => true,
			'supports' => array('custom-fields', 'title', 'thumbnail'), 'publicly_queryable' => true,
			'has_archive' => true, 'taxonomies' => array(),
			'labels' => array (
				'name' => 'Cursos',
				'singular_name' => 'Curso'
			)
		) ;

		static $fields = array(
			'profession' => array('type' => 'richtext', 'label' => 'O que é a profissão'),
			'about' => array('type' => 'richtext', 'label' => 'Na FATEA'),
			'labs' => array('type' => 'richtext', 'label' => 'Laboratórios Utilizados'),
			'projects' => array('type' => 'richtext', 'label' => 'Ações & Projetos de Extensão'),
			'video_url' => array('type' => 'url', 'size' => 80, 'label' => 'Vídeo', 'description' => 'URL do vídeo institucional no Youtube.'),

			'periodicy' => array('type' => 'set', 'label' => 'Periodicidade',
				'values' => array('semestral' => 'Semestral', 'anual' => 'Anual')),
			'spaces' => array('type' => 'integer', 'label' => 'Nº de Vagas'),
			'period' => array('type' => 'set', 'label' => 'Período',
				'values' => array('matinal' => 'Matutino', 'nocturnal' => 'Noturno') ),
			'status' => array('type' => 'text', 'label' => 'Estado legal', 'description' => 'portaria no MEC, etc.'),
			'duration' => array('type' => 'integer', 'label' => 'Duração', 'description' => 'em anos.')
		) ;

		static function build(){
			parent::build(); 
		}



		
	}

 ?>