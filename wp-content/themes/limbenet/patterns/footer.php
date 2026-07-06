<?php
/**
 * Title: Site footer
 * Slug: limbenet/footer
 * Categories: limbenet-layout
 *
 * @package LimbeNet
 */
?>
<!-- wp:html -->
<footer class="lnet-site-footer">
	<div class="lnet-wrap">
		<div class="lnet-footer-grid">
			<div>
				<a class="lnet-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<?php echo esc_html__( 'Limbe.Net', 'limbenet' ); ?>
					<span><?php echo esc_html__( 'Cameroon Travel Guide', 'limbenet' ); ?></span>
				</a>
				<p><?php echo esc_html__( 'An independent tourism guide for planning Cameroon trips with verified information, responsible safety notices, and official links where available.', 'limbenet' ); ?></p>
				<?php echo do_shortcode( '[limbenet_social_links context="footer"]' ); ?>
				<?php echo do_shortcode( '[limbenet_language_switcher context="footer"]' ); ?>
			</div>

			<div>
				<div class="lnet-footer-title"><?php echo esc_html__( 'Explore', 'limbenet' ); ?></div>
				<div class="lnet-footer-links">
					<a href="<?php echo esc_url( home_url( '/places-to-go/' ) ); ?>"><?php echo esc_html__( 'Places to Go', 'limbenet' ); ?></a>
					<a href="<?php echo esc_url( home_url( '/things-to-do/' ) ); ?>"><?php echo esc_html__( 'Things to Do', 'limbenet' ); ?></a>
					<a href="<?php echo esc_url( get_post_type_archive_link( 'itinerary' ) ?: home_url( '/trip-ideas/' ) ); ?>"><?php echo esc_html__( 'Trip Ideas', 'limbenet' ); ?></a>
					<a href="<?php echo esc_url( home_url( '/travel-info/' ) ); ?>"><?php echo esc_html__( 'Travel Info', 'limbenet' ); ?></a>
				</div>
			</div>

			<div>
				<div class="lnet-footer-title"><?php echo esc_html__( 'Book & Save', 'limbenet' ); ?></div>
				<div class="lnet-footer-links">
					<a href="<?php echo esc_url( home_url( '/tickets-tours/' ) ); ?>"><?php echo esc_html__( 'Tickets & Tours', 'limbenet' ); ?></a>
					<a href="<?php echo esc_url( get_post_type_archive_link( 'deal' ) ?: home_url( '/deals/' ) ); ?>"><?php echo esc_html__( 'Deals', 'limbenet' ); ?></a>
					<a href="<?php echo esc_url( home_url( '/request-booking-help/' ) ); ?>"><?php echo esc_html__( 'Request Booking Help', 'limbenet' ); ?></a>
				</div>
			</div>

			<div>
				<div class="lnet-footer-title"><?php echo esc_html__( 'Partners', 'limbenet' ); ?></div>
				<div class="lnet-footer-links">
					<a href="<?php echo esc_url( get_post_type_archive_link( 'partner' ) ?: home_url( '/partners/' ) ); ?>"><?php echo esc_html__( 'Partner Directory', 'limbenet' ); ?></a>
					<a href="<?php echo esc_url( home_url( '/partner-with-us/' ) ); ?>"><?php echo esc_html__( 'Partner With Us', 'limbenet' ); ?></a>
					<a href="<?php echo esc_url( home_url( '/claim-listing/' ) ); ?>"><?php echo esc_html__( 'Claim This Listing', 'limbenet' ); ?></a>
				</div>
			</div>
		</div>

		<div class="lnet-footer-note">
			<?php
			printf(
				/* translators: %s: current year. */
				esc_html__( 'Copyright %s Limbe.Net. Limbe.Net is an independent Cameroon tourism guide and is not an official government portal.', 'limbenet' ),
				esc_html( gmdate( 'Y' ) )
			);
			?>
		</div>
	</div>
</footer>
<!-- /wp:html -->
