<?php
	namespace FateaVestibular ;  
	use CustomPost, DateTime ;

	class Course extends CustomPost {

		static $name = "course" ;
		static $editable_by = array('form_advanced');
		static $creation_fields = array( 
			'label' => 'course','description' => '',
			'public' => true,'show_ui' => true,'show_in_menu' => true,'capability_type' => 'post',
			'hierarchical' => false,'rewrite' => array('slug' => 'cursos'),'query_var' => true,
			'supports' => array('custom-fields', 'title', 'editor', 'thumbnail'), 'publicly_queryable' => true,
			'has_archive' => true, 'taxonomies' => array(),
			'labels' => array (
				'name' => 'Cursos',
				'singular_name' => 'Curso'
			)
		) ;

		static $fields = array(
			'profissao' => array('type' => 'richtext', 'label' => 'O que é a profissão'),
			'mercado' => array('type' => 'richtext', 'label' => 'Oportunidades & Mercado de Trabalho'),
			'na_fatea' => array('type' => 'richtext', 'label' => 'Na FATEA'),
			'laboratorios' => array('type' => 'richtext', 'label' => 'Laboratórios Utilizados'),
			'extensoes' => array('type' => 'richtext', 'label' => 'Ações & Projetos de Extensão')
		) ;

		static function build(){
			parent::build(); 
		}



		
	}

 ?>