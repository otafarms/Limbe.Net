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
		<a class="lnet-brand lnet-header-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr__( 'Limbe.Net home', 'limbenet-festivaltrail' ); ?>">
			<img class="lnet-brand-logo" src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/site-logo.png' ); ?>" alt="<?php echo esc_attr__( 'Limbe.Net', 'limbenet-festivaltrail' ); ?>" width="1425" height="570" decoding="async">
		</a>

		<nav class="lnet-nav" aria-label="<?php echo esc_attr__( 'Primary navigation', 'limbenet-festivaltrail' ); ?>">
			<a href="<?php echo esc_url( home_url( '/places-to-go/' ) ); ?>"><?php echo esc_html__( 'Places to Go', 'limbenet-festivaltrail' ); ?></a>
			<a href="<?php echo esc_url( home_url( '/things-to-do/' ) ); ?>"><?php echo esc_html__( 'Things to Do', 'limbenet-festivaltrail' ); ?></a>
			<a href="<?php echo esc_url( get_post_type_archive_link( 'attraction' ) ?: home_url( '/attractions/' ) ); ?>"><?php echo esc_html__( 'Attractions', 'limbenet-festivaltrail' ); ?></a>
			<a href="<?php echo esc_url( home_url( '/tickets-tours/' ) ); ?>"><?php echo esc_html__( 'Tickets & Tours', 'limbenet-festivaltrail' ); ?></a>
			<a href="<?php echo esc_url( home_url( '/travel-info/' ) ); ?>"><?php echo esc_html__( 'Travel Info', 'limbenet-festivaltrail' ); ?></a>
			<a href="<?php echo esc_url( get_post_type_archive_link( 'deal' ) ?: home_url( '/deals/' ) ); ?>"><?php echo esc_html__( 'Deals', 'limbenet-festivaltrail' ); ?></a>
			<a href="<?php echo esc_url( home_url( '/partner-with-us/' ) ); ?>"><?php echo esc_html__( 'Partner With Us', 'limbenet-festivaltrail' ); ?></a>
		</nav>

		<div class="lnet-header-actions">
			<a class="lnet-search-link" href="<?php echo esc_url( home_url( '/?s=' ) ); ?>" aria-label="<?php echo esc_attr__( 'Search Limbe.Net', 'limbenet-festivaltrail' ); ?>"></a>
			<?php echo do_shortcode( '[limbenet_language_switcher context="header"]' ); ?>
		</div>

		<details class="lnet-mobile-menu">
			<summary><?php echo esc_html__( 'Menu', 'limbenet-festivaltrail' ); ?></summary>
			<div class="lnet-mobile-panel">
				<nav class="lnet-nav" aria-label="<?php echo esc_attr__( 'Mobile navigation', 'limbenet-festivaltrail' ); ?>">
					<a href="<?php echo esc_url( home_url( '/places-to-go/' ) ); ?>"><?php echo esc_html__( 'Places to Go', 'limbenet-festivaltrail' ); ?></a>
					<a href="<?php echo esc_url( home_url( '/things-to-do/' ) ); ?>"><?php echo esc_html__( 'Things to Do', 'limbenet-festivaltrail' ); ?></a>
					<a href="<?php echo esc_url( get_post_type_archive_link( 'attraction' ) ?: home_url( '/attractions/' ) ); ?>"><?php echo esc_html__( 'Attractions', 'limbenet-festivaltrail' ); ?></a>
					<a href="<?php echo esc_url( home_url( '/tickets-tours/' ) ); ?>"><?php echo esc_html__( 'Tickets & Tours', 'limbenet-festivaltrail' ); ?></a>
					<a href="<?php echo esc_url( home_url( '/travel-info/' ) ); ?>"><?php echo esc_html__( 'Travel Info', 'limbenet-festivaltrail' ); ?></a>
					<a href="<?php echo esc_url( get_post_type_archive_link( 'deal' ) ?: home_url( '/deals/' ) ); ?>"><?php echo esc_html__( 'Deals', 'limbenet-festivaltrail' ); ?></a>
					<a href="<?php echo esc_url( home_url( '/partner-with-us/' ) ); ?>"><?php echo esc_html__( 'Partner With Us', 'limbenet-festivaltrail' ); ?></a>
				</nav>
				<?php echo do_shortcode( '[limbenet_language_switcher context="mobile"]' ); ?>
			</div>
		</details>
	</div>
</header>
<!-- /wp:html -->
