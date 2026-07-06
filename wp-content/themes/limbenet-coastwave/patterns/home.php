<?php
/**
 * Title: Limbe.Net home
 * Slug: limbenet/home
 * Categories: limbenet-layout
 *
 * @package LimbeNet
 */
?>
<!-- wp:html -->
<main id="main">
	<section class="lnet-hero">
		<div class="lnet-wrap lnet-hero-inner">
			<div class="lnet-hero-copy">
				<p class="lnet-kicker"><?php echo esc_html__( 'Independent Cameroon tourism guide', 'limbenet-coastwave' ); ?></p>
				<h1><?php echo esc_html__( 'Explore Cameroon: beaches, mountains, wildlife, culture, and unforgettable local experiences.', 'limbenet-coastwave' ); ?></h1>
				<p><?php echo esc_html__( 'Plan trips with destination guides, attraction details, safety notices, ticket instructions, partner listings, and travel help built for real visitors.', 'limbenet-coastwave' ); ?></p>
				<div class="lnet-search-panel">
					<?php echo do_shortcode( '[limbenet_tourism_search compact="true" placeholder="' . esc_attr__( 'Where do you want to go?', 'limbenet-coastwave' ) . '"]' ); ?>
				</div>
				<div class="lnet-hero-links">
					<a class="lnet-button" href="<?php echo esc_url( get_post_type_archive_link( 'attraction' ) ?: home_url( '/attractions/' ) ); ?>"><?php echo esc_html__( 'Explore Attractions', 'limbenet-coastwave' ); ?></a>
					<a class="lnet-button-outline" href="<?php echo esc_url( home_url( '/travel-info/' ) ); ?>"><?php echo esc_html__( 'Plan Safely', 'limbenet-coastwave' ); ?></a>
				</div>
			</div>
		</div>
	</section>

	<section class="lnet-trust-strip">
		<div class="lnet-wrap lnet-trust-grid">
			<div><strong><?php echo esc_html__( 'Verified-first information', 'limbenet-coastwave' ); ?></strong><span><?php echo esc_html__( 'Last updated fields and source notes are part of every listing.', 'limbenet-coastwave' ); ?></span></div>
			<div><strong><?php echo esc_html__( 'Responsible safety notices', 'limbenet-coastwave' ); ?></strong><span><?php echo esc_html__( 'Travel planning boxes are visible on attractions and destinations.', 'limbenet-coastwave' ); ?></span></div>
			<div><strong><?php echo esc_html__( 'Independent guide', 'limbenet-coastwave' ); ?></strong><span><?php echo esc_html__( 'Limbe.Net is not an official government portal.', 'limbenet-coastwave' ); ?></span></div>
		</div>
	</section>

	<section class="lnet-section">
		<div class="lnet-wrap">
			<div class="lnet-section-header">
				<div>
					<p class="lnet-kicker"><?php echo esc_html__( 'Places to go', 'limbenet-coastwave' ); ?></p>
					<h2><?php echo esc_html__( 'Featured destinations', 'limbenet-coastwave' ); ?></h2>
				</div>
				<a href="<?php echo esc_url( get_post_type_archive_link( 'destination' ) ?: home_url( '/places-to-go/' ) ); ?>"><?php echo esc_html__( 'View all places', 'limbenet-coastwave' ); ?></a>
			</div>
			<?php echo do_shortcode( '[limbenet_featured type="destination" limit="6"]' ); ?>
		</div>
	</section>

	<section class="lnet-section is-tinted">
		<div class="lnet-wrap">
			<div class="lnet-section-header">
				<div>
					<p class="lnet-kicker"><?php echo esc_html__( 'Popular now', 'limbenet-coastwave' ); ?></p>
					<h2><?php echo esc_html__( 'Popular attractions', 'limbenet-coastwave' ); ?></h2>
				</div>
				<a href="<?php echo esc_url( get_post_type_archive_link( 'attraction' ) ?: home_url( '/attractions/' ) ); ?>"><?php echo esc_html__( 'Browse attractions', 'limbenet-coastwave' ); ?></a>
			</div>
			<?php echo do_shortcode( '[limbenet_featured type="attraction" limit="6"]' ); ?>
		</div>
	</section>

	<section class="lnet-section">
		<div class="lnet-wrap">
			<div class="lnet-section-header">
				<div>
					<p class="lnet-kicker"><?php echo esc_html__( 'Things to do', 'limbenet-coastwave' ); ?></p>
					<h2><?php echo esc_html__( 'Explore by travel style', 'limbenet-coastwave' ); ?></h2>
				</div>
			</div>
			<?php echo do_shortcode( '[limbenet_travel_styles]' ); ?>
		</div>
	</section>

	<section class="lnet-section is-tinted">
		<div class="lnet-wrap lnet-editorial-grid">
			<div>
				<div class="lnet-section-header">
					<div>
						<p class="lnet-kicker"><?php echo esc_html__( 'Plan your trip', 'limbenet-coastwave' ); ?></p>
						<h2><?php echo esc_html__( 'Travel information for Cameroon', 'limbenet-coastwave' ); ?></h2>
					</div>
				</div>
				<?php echo do_shortcode( '[limbenet_plan_trip]' ); ?>
			</div>
			<div>
				<?php echo do_shortcode( '[limbenet_ticket_help]' ); ?>
			</div>
		</div>
	</section>

	<section class="lnet-section">
		<div class="lnet-wrap">
			<div class="lnet-section-header">
				<div>
					<p class="lnet-kicker"><?php echo esc_html__( 'Trip ideas', 'limbenet-coastwave' ); ?></p>
					<h2><?php echo esc_html__( 'Latest travel guides', 'limbenet-coastwave' ); ?></h2>
				</div>
				<a href="<?php echo esc_url( get_post_type_archive_link( 'itinerary' ) ?: home_url( '/trip-ideas/' ) ); ?>"><?php echo esc_html__( 'See itineraries', 'limbenet-coastwave' ); ?></a>
			</div>
			<?php echo do_shortcode( '[limbenet_featured type="itinerary" limit="3"]' ); ?>
		</div>
	</section>

	<section class="lnet-section is-tinted">
		<div class="lnet-wrap">
			<div class="lnet-section-header">
				<div>
					<p class="lnet-kicker"><?php echo esc_html__( 'Deals', 'limbenet-coastwave' ); ?></p>
					<h2><?php echo esc_html__( 'Featured deals', 'limbenet-coastwave' ); ?></h2>
				</div>
				<a href="<?php echo esc_url( get_post_type_archive_link( 'deal' ) ?: home_url( '/deals/' ) ); ?>"><?php echo esc_html__( 'View deals', 'limbenet-coastwave' ); ?></a>
			</div>
			<?php echo do_shortcode( '[limbenet_featured type="deal" limit="3"]' ); ?>
		</div>
	</section>

	<section class="lnet-section is-deep">
		<div class="lnet-wrap">
			<?php echo do_shortcode( '[limbenet_partner_cta]' ); ?>
		</div>
	</section>

	<section class="lnet-section">
		<div class="lnet-wrap">
			<?php echo do_shortcode( '[limbenet_newsletter]' ); ?>
		</div>
	</section>
</main>
<!-- /wp:html -->
