<?php
/**
 * Fallback template for hosts that load PHP templates before block templates.
 *
 * @package LimbeNet
 */
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php
if ( function_exists( 'block_template_part' ) ) {
	block_template_part( 'header' );
}
?>
<main class="wp-block-group lnet-main">
	<div class="wp-block-group lnet-wrap">
		<?php if ( have_posts() ) : ?>
			<?php while ( have_posts() ) : ?>
				<?php the_post(); ?>
				<article <?php post_class(); ?>>
					<?php the_title( '<h1>', '</h1>' ); ?>
					<?php the_content(); ?>
				</article>
			<?php endwhile; ?>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<p>No content found.</p>
		<?php endif; ?>
	</div>
</main>
<?php
if ( function_exists( 'block_template_part' ) ) {
	block_template_part( 'footer' );
}
wp_footer();
?>
</body>
</html>
