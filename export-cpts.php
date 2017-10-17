<?php

/*
Plugin Name: Export Contents
Description: Plugin para exportação de posts de custom post types do wordpress.
Version: 1.1.2
Author: Cleiton
*/

/*********  EXPORTAÇÃO DOS CPTS (CUSTOM POST TYPES) *********/


$cpts = array('seus_cpts','mais_cpts...');

$export_fields = array(
	'post_title',
	'post_content',
	'post_date'
);

/********  PARAMETROS QUE SERÃO EXPORTADOS NO WP_QUERY  ********/

$export_query = array(
	'posts_per_page' => -1,
	'post_status' => 'publish',
	'orderby' => 'post_type',
	'post_type' => $cpts, 
);

// CONSULTA DE POSTS

$posts = new WP_Query( $export_query );
$posts = $posts->posts;

// NOME DE SAIDA DO ARQUIVO

$output_filename = 'nome_do_seu_arquivo_' . strftime( '%Y-%m-%d' )  . '.csv';
$output_handle = @fopen( 'php://output', 'w' );
header( 'Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate');
header( 'Content-Description: File Transfer' );
header( 'Content-type: text/csv' );
header( 'Content-Disposition: attachment; filename=' . $output_filename );
header( 'Expires: 0' );
header( 'Pragma: public' );

// DADOS DO ARQUIVO EXPORTADO

foreach ( $posts as $post ) {

	// GET PERMALINK DO POST

	switch ( $post->post_type ) {
		case 'revision':
		case 'nav_menu_item':
			break;
		case 'page':
			$permalink = get_page_link( $post->ID );
			break;
		case 'post':
			$permalink = get_permalink( $post->ID );
			break;
		case 'attachment':
			$permalink = get_attachment_link( $post->ID );
			break;
		default:
			$permalink = get_post_permalink( $post->ID );
			break;
	}

	// MONTANDO ARRAY

	$post_export = array( $permalink );
	foreach ( $export_fields as $export_field ) {
		$post_export[] = $post->$export_field;
	}

	// ADICIONANDO LINHAS AO ARQUIVO

	fputcsv( $output_handle, $post_export );
}
	fclose( $output_handle );
	
exit;	