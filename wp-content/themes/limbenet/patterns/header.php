<?php
/**
 * Title: Site header
 * Slug: limbenet/header
 * Categories: limbenet-layout
 *
 * @package LimbeNet
 */
?>
<!-- wp:html -->
<header class="lnet-site-header">
	<div class="lnet-wrap lnet-header-inner">
		<a class="lnet-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php echo esc_html__( 'Limbe.Net', 'limbenet' ); ?>
			<span><?php echo esc_html__( 'Cameroon Travel Guide', 'limbenet' ); ?></span>
		</a>

		<nav class="lnet-nav" aria-label="<?php echo esc_attr__( 'Primary navigation', 'limbenet' ); ?>">
			<a href="<?php echo esc_url( home_url( '/places-to-go/' ) ); ?>"><?php echo esc_html__( 'Places to Go', 'limbenet' ); ?></a>
			<a href="<?php echo esc_url( home_url( '/things-to-do/' ) ); ?>"><?php echo esc_html__( 'Things to Do', 'limbenet' ); ?></a>
			<a href="<?php echo esc_url( get_post_type_archive_link( 'attraction' ) ?: home_url( '/attractions/' ) ); ?>"><?php echo esc_html__( 'Attractions', 'limbenet' ); ?></a>
			<a href="<?php echo esc_url( home_url( '/tickets-tours/' ) ); ?>"><?php echo esc_html__( 'Tickets & Tours', 'limbenet' ); ?></a>
			<a href="<?php echo esc_url( home_url( '/travel-info/' ) ); ?>"><?php echo esc_html__( 'Travel Info', 'limbenet' ); ?></a>
			<a href="<?php echo esc_url( get_post_type_archive_link( 'deal' ) ?: home_url( '/deals/' ) ); ?>"><?php echo esc_html__( 'Deals', 'limbenet' ); ?></a>
			<a href="<?php echo esc_url( home_url( '/partner-with-us/' ) ); ?>"><?php echo esc_html__( 'Partner With Us', 'limbenet' ); ?></a>
		</nav>

		<div class="lnet-header-actions">
			<a class="lnet-search-link" href="<?php echo esc_url( home_url( '/?s=' ) ); ?>" aria-label="<?php echo esc_attr__( 'Search Limbe.Net', 'limbenet' ); ?>"></a>
			<?php echo do_shortcode( '[limbenet_language_switcher context="header"]' ); ?>
		</div>

		<details class="lnet-mobile-menu">
			<summary><?php echo esc_html__( 'Menu', 'limbenet' ); ?></summary>
			<div class="lnet-mobile-panel">
				<nav class="lnet-nav" aria-label="<?php echo esc_attr__( 'Mobile navigation', 'limbenet' ); ?>">
					<a href="<?php echo esc_url( home_url( '/places-to-go/' ) ); ?>"><?php echo esc_html__( 'Places to Go', 'limbenet' ); ?></a>
					<a href="<?php echo esc_url( home_url( '/things-to-do/' ) ); ?>"><?php echo esc_html__( 'Things to Do', 'limbenet' ); ?></a>
					<a href="<?php echo esc_url( get_post_type_archive_link( 'attraction' ) ?: home_url( '/attractions/' ) ); ?>"><?php echo esc_html__( 'Attractions', 'limbenet' ); ?></a>
					<a href="<?php echo esc_url( home_url( '/tickets-tours/' ) ); ?>"><?php echo esc_html__( 'Tickets & Tours', 'limbenet' ); ?></a>
					<a href="<?php echo esc_url( home_url( '/travel-info/' ) ); ?>"><?php echo esc_html__( 'Travel Info', 'limbenet' ); ?></a>
					<a href="<?php echo esc_url( get_post_type_archive_link( 'deal' ) ?: home_url( '/deals/' ) ); ?>"><?php echo esc_html__( 'Deals', 'limbenet' ); ?></a>
					<a href="<?php echo esc_url( home_url( '/partner-with-us/' ) ); ?>"><?php echo esc_html__( 'Partner With Us', 'limbenet' ); ?></a>
				</nav>
				<?php echo do_shortcode( '[limbenet_language_switcher context="mobile"]' ); ?>
			</div>
		</details>
	</div>
</header>
<!-- /wp:html -->
