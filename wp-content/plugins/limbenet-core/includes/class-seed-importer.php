<?php
/**
 * Seed content importer.
 *
 * @package LimbeNetCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Imports starter tourism content.
 */
class LimbeNet_Core_Seed_Importer {
	/**
	 * Register admin page.
	 */
	public function register_menu() {
		add_submenu_page(
			'limbenet-tourism',
			__( 'Seed Importer', 'limbenet-core' ),
			__( 'Seed Importer', 'limbenet-core' ),
			'manage_options',
			'limbenet-seed-importer',
			array( $this, 'render_page' )
		);
	}

	/**
	 * Render importer page.
	 */
	public function render_page() {
		$count = isset( $_GET['imported'] ) ? absint( $_GET['imported'] ) : 0;
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Limbe.Net Seed Importer', 'limbenet-core' ); ?></h1>
			<?php if ( $count ) : ?>
				<div class="notice notice-success"><p><?php echo esc_html( sprintf( __( 'Seed import completed. %d items were created or updated.', 'limbenet-core' ), $count ) ); ?></p></div>
			<?php endif; ?>
			<p><?php esc_html_e( 'Import starter pages, taxonomies, attractions, destinations, travel info pages, itineraries, partners, deals, and events. Prices stay unverified and safety notices remain visible placeholders.', 'limbenet-core' ); ?></p>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php wp_nonce_field( 'limbenet_import_seed', 'limbenet_seed_nonce' ); ?>
				<input type="hidden" name="action" value="limbenet_import_seed">
				<?php submit_button( __( 'Import Seed Content', 'limbenet-core' ), 'primary' ); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Handle import request.
	 */
	public function handle_import() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to import seed content.', 'limbenet-core' ) );
		}

		if ( empty( $_POST['limbenet_seed_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['limbenet_seed_nonce'] ) ), 'limbenet_import_seed' ) ) {
			wp_die( esc_html__( 'The import request could not be verified.', 'limbenet-core' ) );
		}

		$count = $this->import();
		wp_safe_redirect( add_query_arg( 'imported', absint( $count ), admin_url( 'admin.php?page=limbenet-seed-importer' ) ) );
		exit;
	}

	/**
	 * Import content.
	 *
	 * @return int Created/updated count.
	 */
	private function import() {
		$count = 0;
		$count += $this->import_terms();
		$count += $this->import_pages();
		$count += $this->import_destinations();
		$count += $this->import_attractions();
		$count += $this->import_travel_info();
		$count += $this->import_itineraries();
		$count += $this->import_partners();
		$count += $this->import_deals();
		$count += $this->import_events();

		return $count;
	}

	/**
	 * Import taxonomy terms.
	 *
	 * @return int Count.
	 */
	private function import_terms() {
		$count = 0;
		$terms = array(
			'region'          => array( 'South West', 'Littoral', 'Centre', 'South Region', 'West Region', 'East Region', 'North Cameroon' ),
			'city'            => array( 'Limbe', 'Buea', 'Douala', 'Yaounde', 'Kribi', 'Foumban', 'Bamenda', 'Rhumsiki' ),
			'attraction_type' => array( 'Beaches', 'Wildlife & Safari', 'Mountains & Hiking', 'Culture & Heritage', 'Food & Nightlife', 'History', 'Festivals & Events', 'Eco-tourism', 'Family Trips', 'Weekend Trips' ),
			'travel_style'    => array( 'Beaches', 'Wildlife & Safari', 'Mountains & Hiking', 'Culture & Heritage', 'Food & Nightlife', 'History', 'Festivals & Events', 'Eco-tourism', 'Family Trips', 'Weekend Trips' ),
			'partner_type'    => array( 'Hotel', 'Restaurant', 'Tour Guide', 'Transport', 'Attraction', 'Event', 'Photographer', 'Other' ),
			'difficulty'      => array( 'Easy', 'Moderate', 'Challenging' ),
			'budget_range'    => array( 'Budget', 'Mid-range', 'Premium' ),
			'safety_status'   => array( 'Normal travel planning', 'Check current advisory before travel', 'High-risk area: travel only with expert local guidance' ),
		);

		foreach ( $terms as $taxonomy => $names ) {
			foreach ( $names as $name ) {
				$result = $this->ensure_term( $taxonomy, $name );
				if ( $result ) {
					$count++;
				}
			}
		}

		return $count;
	}

	/**
	 * Import core pages.
	 *
	 * @return int Count.
	 */
	private function import_pages() {
		$count = 0;
		$pages = array(
			array( 'Home', 'home', '' ),
			array( 'About Us', 'about-us', $this->about_page_content() ),
			array( 'Contact Us', 'contact-us', '[limbenet_contact_page]' ),
			array( 'Terms & Conditions', 'terms-and-conditions', $this->terms_page_content(), false ),
			array( 'Privacy Policy', 'privacy-policy', $this->privacy_page_content(), false ),
			array( 'Cookie Policy', 'cookie-policy', $this->cookie_page_content(), false ),
			array( 'Things to Do', 'things-to-do', '[limbenet_travel_styles]' ),
			array( 'Tickets & Tours', 'tickets-tours', '[limbenet_ticket_help expanded="true"][limbenet_booking_form]' ),
			array( 'Travel Info', 'travel-info', '[limbenet_travel_info]' ),
			array( 'Partner Directory', 'partners-directory', '[limbenet_tourism_search post_type="partner" button_label="Filter partners"]' ),
			array( 'Partner With Us', 'partner-with-us', '[limbenet_partner_form]' ),
			array( 'Request Booking Help', 'request-booking-help', '[limbenet_booking_form]' ),
			array( 'Claim Listing', 'claim-listing', '[limbenet_claim_form]' ),
			array( 'Advertise With Limbe.Net', 'advertise-with-limbenet', '[limbenet_advertise_form]' ),
			array( 'Blog / Magazine', 'blog', '' ),
		);

		foreach ( $pages as $page ) {
			$page_id = $this->ensure_page( $page[0], $page[1], $page[2], isset( $page[3] ) ? (bool) $page[3] : true );
			if ( $page_id ) {
				$count++;
			}

			if ( 'home' === $page[1] ) {
				update_option( 'show_on_front', 'page' );
				update_option( 'page_on_front', $page_id );
			}

			if ( 'blog' === $page[1] ) {
				update_option( 'page_for_posts', $page_id );
			}
		}

		return $count;
	}

	/**
	 * Get seeded About Us page content.
	 *
	 * @return string
	 */
	private function about_page_content() {
		$hero_image        = LIMBENET_CORE_URL . 'assets/images/limbenet-hero.png';
		$limbe_image       = LIMBENET_CORE_URL . 'assets/images/limbe-city-featured.webp';
		$destination_image = LIMBENET_CORE_URL . 'assets/images/home-featured-destinations.webp';
		$attraction_image  = LIMBENET_CORE_URL . 'assets/images/home-popular-attractions.webp';

		return '<section class="lnet-about-page">
	<div class="lnet-about-layout">
		<div class="lnet-about-gallery" aria-label="Limbe.Net Cameroon tourism visuals">
			<img class="is-wide" src="' . esc_url( $hero_image ) . '" alt="Cameroon coast and mountain travel inspiration" loading="lazy">
			<img src="' . esc_url( $limbe_image ) . '" alt="Limbe city and coastline" loading="lazy">
			<img src="' . esc_url( $attraction_image ) . '" alt="Cameroon attractions and culture" loading="lazy">
			<img class="is-wide" src="' . esc_url( $destination_image ) . '" alt="Featured Cameroon destinations" loading="lazy">
		</div>

		<div class="lnet-about-copy">
			<p class="lnet-kicker">Independent Cameroon tourism guide</p>
			<h2>Who are we?</h2>
			<p>Limbe.Net is an independent digital tourism guide created to help travelers discover Limbe and the wider Cameroon travel experience with practical, locally grounded information.</p>
			<p>We bring destinations, attractions, travel tips, partner listings, booking-help requests, and responsible safety notices into one simple planning hub. Our goal is to make it easier for visitors and residents to explore Cameroon with confidence, curiosity, and respect for local communities.</p>
			<p>Limbe.Net is not an official government portal. We are a tourism discovery platform that encourages travelers to verify visas, safety advisories, health guidance, prices, routes, and availability with official sources and service providers before booking.</p>
			<a class="lnet-button" href="' . esc_url( home_url( '/places-to-go/' ) ) . '">Explore Cameroon</a>
		</div>
	</div>

	<div class="lnet-about-pillars">
		<div>
			<h3>What we do</h3>
			<p>We organize Cameroon travel information into useful pages for destinations, attractions, travel ideas, ticket planning, deals, and partner discovery.</p>
		</div>
		<div>
			<h3>How we help travelers</h3>
			<p>We highlight practical details, official links where available, and clear safety reminders so visitors can plan with better context.</p>
		</div>
		<div>
			<h3>How we support partners</h3>
			<p>We give hotels, restaurants, guides, transport providers, attractions, photographers, and event organizers a path to request listings and visibility.</p>
		</div>
	</div>
</section>';
	}

	/**
	 * Get seeded Terms & Conditions page content.
	 *
	 * @return string
	 */
	private function terms_page_content() {
		$contact_url = home_url( '/contact-us/' );

		return '<section class="lnet-legal-page">
	<div class="lnet-legal-notice">
		<p>These Terms & Conditions are a starter template for Limbe.Net and should be reviewed by a qualified legal professional before public launch. By accessing or using Limbe.Net, you agree to these terms.</p>
	</div>

	<div class="lnet-legal-grid">
		<section>
			<h3>1. About Limbe.Net</h3>
			<p>Limbe.Net is an independent Cameroon tourism guide that publishes destination information, attractions, travel guidance, partner listings, deals, events, booking-help forms, and related content. Limbe.Net is not an official government portal, embassy, airline, hotel, tour operator, or public authority.</p>
			<p>Information on the site is provided for general travel planning and discovery. Travelers are responsible for verifying visas, health requirements, safety advisories, prices, availability, routes, dates, and provider credentials before making travel decisions.</p>
		</section>

		<section>
			<h3>2. Acceptance of these terms</h3>
			<p>By visiting, browsing, submitting a form, requesting booking help, claiming a listing, advertising, or otherwise using Limbe.Net, you agree to follow these Terms & Conditions and any policies linked from this site.</p>
			<p>If you do not agree with these terms, please do not use the site.</p>
		</section>

		<section>
			<h3>3. Changes to these terms</h3>
			<p>Limbe.Net may update these Terms & Conditions from time to time. Updated terms will be posted on this page with a revised last-updated date. Continued use of the site after changes are posted means you accept the updated terms.</p>
		</section>

		<section>
			<h3>4. Intellectual property</h3>
			<p>Unless otherwise stated, site design, text, page layouts, logos, graphics, photography, code, guides, directories, and other content on Limbe.Net are owned by Limbe.Net or used with permission. This content is protected by applicable copyright, trademark, and intellectual property laws.</p>
			<p>You may view, share, and link to Limbe.Net pages for personal, non-commercial travel planning. You may not copy, scrape, republish, sell, modify, reproduce, or create derivative works from site content without prior written permission, except where allowed by law.</p>
		</section>

		<section>
			<h3>5. Permitted use</h3>
			<p>You agree not to misuse Limbe.Net, interfere with site security, attempt unauthorized access, submit malicious code, overload the site, impersonate another person or organization, or use the site for unlawful, misleading, abusive, or fraudulent activity.</p>
		</section>

		<section>
			<h3>6. User submissions and partner listings</h3>
			<p>If you submit messages, listing requests, claims, reviews, photos, corrections, advertisements, or other materials, you confirm that the information is accurate, lawful, and that you have the right to submit it.</p>
			<p>You grant Limbe.Net permission to review, edit, format, publish, translate, remove, or decline submitted material for site operations, moderation, safety, quality control, marketing, and directory management. Limbe.Net may reject or remove listings that are incomplete, misleading, unsafe, unlawful, outdated, or inconsistent with our editorial standards.</p>
		</section>

		<section>
			<h3>7. Accounts, access, and termination</h3>
			<p>If Limbe.Net later offers user accounts, partner dashboards, paid placements, or contributor access, you are responsible for keeping login details secure and for activity under your account.</p>
			<p>Limbe.Net may suspend, restrict, terminate, remove, or refuse access to accounts, listings, submissions, booking requests, advertising placements, or site features if we believe a user has violated these terms, supplied inaccurate information, harmed other users, created security risk, or acted unlawfully.</p>
		</section>

		<section>
			<h3>8. Third-party providers and links</h3>
			<p>Limbe.Net may link to hotels, restaurants, guides, transport providers, attractions, booking platforms, government websites, payment services, social media platforms, and other third-party websites. Third-party services are controlled by their own operators and may have separate terms, privacy policies, refund rules, cancellation rules, and safety practices.</p>
			<p>Limbe.Net is not responsible for third-party websites, pricing, availability, service quality, cancellations, accidents, delays, losses, or disputes between travelers and providers.</p>
		</section>

		<section>
			<h3>9. Booking help, prices, and availability</h3>
			<p>Booking-help forms and partner contact features are inquiry tools only. Submitting a request does not create a confirmed reservation, ticket, package, contract, or payment obligation unless a separate written agreement is made with Limbe.Net or a third-party provider.</p>
			<p>Prices, schedules, ticket requirements, travel times, opening hours, and availability may change without notice. Items marked as unverified or needing verification should not be treated as confirmed information.</p>
		</section>

		<section>
			<h3>10. Payments, refunds, cancellations, and shipping</h3>
			<p>Limbe.Net currently functions primarily as an information, directory, advertising, and lead-request platform. Unless clearly stated at checkout or in a written agreement, Limbe.Net does not directly sell travel services, hotel stays, transport, tickets, or physical goods.</p>
			<p>If Limbe.Net later sells advertising, sponsored placements, digital services, partner packages, or other paid services, payment, cancellation, and refund terms will be stated at the point of purchase or in the applicable written agreement. Digital and advertising services may be non-refundable once work begins, unless otherwise required by law or agreed in writing.</p>
			<p>Limbe.Net does not currently ship physical products. If physical products are offered in the future, shipping timelines, delivery options, returns, and refund rules will be provided before purchase.</p>
			<p>Payments made to third-party providers are governed by that provider&apos;s own refund, cancellation, and shipping policies.</p>
		</section>

		<section>
			<h3>11. Affiliate links, advertising, and sponsored content</h3>
			<p>Some links, listings, deals, articles, or placements may be sponsored, paid, affiliate, or partner content. Limbe.Net may earn compensation when users click links, submit inquiries, book through partners, or purchase from third parties. Sponsored or affiliate relationships do not guarantee quality, safety, availability, or suitability.</p>
		</section>

		<section>
			<h3>12. Travel safety and health disclaimer</h3>
			<p>Travel involves risk. Conditions can change quickly because of weather, road conditions, health issues, civil unrest, local regulations, wildlife, ocean conditions, mountain conditions, or provider operations. Limbe.Net does not guarantee that any destination, attraction, route, event, provider, or activity is safe or suitable for every traveler.</p>
			<p>Before travel, check current official advisories, entry requirements, health guidance, insurance coverage, and local instructions. Use qualified local guides where appropriate.</p>
		</section>

		<section>
			<h3>13. No professional advice</h3>
			<p>Content on Limbe.Net is provided for general information only. It is not legal, medical, immigration, financial, insurance, safety, or professional advice. You should consult qualified professionals or official sources for decisions that require expert guidance.</p>
		</section>

		<section>
			<h3>14. Disclaimers and limitation of liability</h3>
			<p>Limbe.Net is provided on an as-is and as-available basis. We do not promise that the site will be uninterrupted, error-free, secure, fully accurate, complete, or current.</p>
			<p>To the maximum extent permitted by law, Limbe.Net and its owners, contributors, partners, contractors, and affiliates are not liable for indirect, incidental, consequential, special, punitive, or economic damages arising from use of the site, reliance on site content, third-party services, travel decisions, or unavailable site features.</p>
		</section>

		<section>
			<h3>15. Indemnity</h3>
			<p>You agree to defend, indemnify, and hold Limbe.Net harmless from claims, losses, liabilities, damages, costs, and expenses arising from your use of the site, your submissions, your violation of these terms, or your violation of another person&apos;s rights.</p>
		</section>

		<section>
			<h3>16. Governing law and disputes</h3>
			<p>These Terms & Conditions should be adapted to the final registered business location and governing law for Limbe.Net. Unless mandatory consumer protection rules say otherwise, disputes should first be addressed by contacting Limbe.Net in good faith before formal proceedings are started.</p>
		</section>

		<section>
			<h3>17. Contact</h3>
			<p>Questions about these Terms & Conditions can be sent through the <a href="' . esc_url( $contact_url ) . '">Contact Us</a> page.</p>
		</section>
	</div>
</section>';
	}

	/**
	 * Get seeded Privacy Policy page content.
	 *
	 * @return string
	 */
	private function privacy_page_content() {
		$contact_url = home_url( '/contact-us/' );
		$updated     = gmdate( 'F j, Y' );

		return '<section class="lnet-legal-page">
	<div class="lnet-legal-notice">
		<p><strong>Last updated: ' . esc_html( $updated ) . '</strong></p>
		<p>This Privacy Policy is a starter template for Limbe.Net and should be reviewed by a qualified legal professional before public launch. It explains what personal information Limbe.Net may collect, how it may be used, when it may be shared, and the choices available to visitors, travelers, partners, and listing owners.</p>
	</div>

	<div class="lnet-legal-grid">
		<section>
			<h3>1. Who we are</h3>
			<p>Limbe.Net is an independent Cameroon tourism guide. We publish travel information, destination guides, attraction pages, partner listings, deals, events, booking-help forms, and related tourism content. Limbe.Net is not an official government portal.</p>
			<p>This Privacy Policy applies to personal information collected through Limbe.Net, related forms, email communications, partner listing requests, advertising inquiries, and similar site features.</p>
		</section>

		<section>
			<h3>2. Information we collect</h3>
			<p>We may collect information you provide directly, such as your name, email address, phone or WhatsApp number, city, business name, business type, listing details, claim requests, advertising inquiries, booking-help messages, corrections, feedback, and any files or text you choose to submit.</p>
			<p>We may collect technical and usage information automatically, such as IP address, browser type, device type, operating system, referring pages, pages viewed, approximate location derived from IP address, date and time of visits, and interactions with site features.</p>
			<p>If we later offer paid advertising, sponsored placements, subscriptions, ticketing, or other paid services, we may collect transaction details needed to process or document those services. Payment card details should be handled by payment processors and should not be stored directly by Limbe.Net unless clearly disclosed.</p>
		</section>

		<section>
			<h3>3. How we use information</h3>
			<p>We may use personal information to respond to messages, process booking-help requests, review partner listings, verify listing claims, manage advertising inquiries, improve site content, maintain security, detect abuse, personalize communications, measure site performance, and operate Limbe.Net.</p>
			<p>We may also use information to send service messages, respond to legal requests, enforce site terms, protect users and partners, comply with applicable law, and support business administration.</p>
		</section>

		<section>
			<h3>4. Legal bases and consent</h3>
			<p>Depending on where you live, our legal bases for processing may include your consent, performance of a requested service, legitimate interests in operating and improving Limbe.Net, compliance with legal obligations, and protection of rights, safety, and security.</p>
			<p>Where consent is required, you may withdraw it by contacting us or using available unsubscribe or preference tools. Withdrawal does not affect processing that occurred before consent was withdrawn.</p>
		</section>

		<section>
			<h3>5. Cookies, analytics, and similar technologies</h3>
			<p>Limbe.Net may use cookies, local storage, analytics tools, security tools, embedded maps, social links, and similar technologies to operate the site, remember preferences, understand site traffic, protect against abuse, and improve user experience.</p>
			<p>Third-party services, such as analytics providers, map providers, social media platforms, advertising partners, video embeds, or booking partners, may collect information according to their own privacy notices. Browser settings may allow you to block or delete cookies, but some site features may not work properly without them.</p>
		</section>

		<section>
			<h3>6. How we share information</h3>
			<p>We may share information with service providers that help us host, secure, analyze, maintain, translate, or improve the site. We may share booking-help or listing-related details with relevant tourism partners when needed to respond to your request.</p>
			<p>We may share information if required by law, legal process, public authorities, safety concerns, fraud prevention, rights protection, business transfers, or with your consent. We do not sell personal information in the ordinary meaning of selling customer lists for money. If this practice changes, we will update this policy and provide legally required choices.</p>
		</section>

		<section>
			<h3>7. Partner listings and public content</h3>
			<p>Information submitted for business listings, events, attractions, deals, claims, or partner pages may be reviewed, edited, and published on Limbe.Net if approved. Do not submit confidential personal information for public listing fields.</p>
			<p>Published listing details may include business names, locations, contact details, websites, social media links, descriptions, images, and service information that you or a representative provides or authorizes.</p>
		</section>

		<section>
			<h3>8. International users and transfers</h3>
			<p>Limbe.Net may be accessed from Cameroon, the United States, the European Economic Area, the United Kingdom, and other locations. Information may be processed in countries where we, our hosting providers, email providers, analytics providers, or other service providers operate.</p>
			<p>Privacy laws may differ by country. Where required, we will use reasonable safeguards for international transfers and respect applicable privacy rights.</p>
		</section>

		<section>
			<h3>9. Data retention</h3>
			<p>We keep personal information only as long as reasonably necessary for the purposes described in this policy, including responding to requests, maintaining records, resolving disputes, improving site operations, meeting legal obligations, and protecting the site.</p>
			<p>Retention periods may vary depending on the type of information, the reason it was collected, legal requirements, security needs, and whether deletion has been requested.</p>
		</section>

		<section>
			<h3>10. Security</h3>
			<p>We use reasonable administrative, technical, and organizational safeguards designed to protect personal information. No website, email, hosting provider, form, or internet transmission can be guaranteed completely secure.</p>
			<p>Please avoid sending sensitive information unless it is necessary for your request.</p>
		</section>

		<section>
			<h3>11. Your privacy choices and rights</h3>
			<p>Depending on your location, you may have rights to request access to personal information, correction, deletion, restriction, objection, portability, withdrawal of consent, or information about how personal information is used and shared.</p>
			<p>You may also unsubscribe from marketing emails where an unsubscribe option is provided. We may need to verify your request before acting on it, and some information may be retained where required or permitted by law.</p>
		</section>

		<section>
			<h3>12. Children</h3>
			<p>Limbe.Net is intended for a general audience and is not directed to children under 13. We do not knowingly collect personal information from children under 13. If you believe a child has provided personal information, please contact us so we can review and delete it where appropriate.</p>
		</section>

		<section>
			<h3>13. Third-party links</h3>
			<p>Limbe.Net may link to third-party websites, booking platforms, government websites, social media platforms, hotels, restaurants, guides, attractions, transport providers, and advertisers. Those third parties control their own privacy practices. Review their privacy policies before submitting information to them.</p>
		</section>

		<section>
			<h3>14. Changes to this policy</h3>
			<p>We may update this Privacy Policy from time to time. Updated versions will be posted on this page. If changes are material, we may provide additional notice where appropriate.</p>
		</section>

		<section>
			<h3>15. Contact</h3>
			<p>Questions or privacy requests can be sent through the <a href="' . esc_url( $contact_url ) . '">Contact Us</a> page.</p>
		</section>
	</div>
</section>';
	}

	/**
	 * Get seeded Cookie Policy page content.
	 *
	 * @return string
	 */
	private function cookie_page_content() {
		$contact_url = home_url( '/contact-us/' );
		$updated     = gmdate( 'F j, Y' );

		return '<section class="lnet-legal-page">
	<div class="lnet-legal-notice">
		<p><strong>Last updated: ' . esc_html( $updated ) . '</strong></p>
		<p>This Cookie Policy is a starter template for Limbe.Net and should be reviewed by a qualified legal professional before public launch. It explains how Limbe.Net may use cookies, local storage, pixels, embedded content, and similar technologies, and how visitors can manage cookie choices.</p>
	</div>

	<div class="lnet-legal-grid">
		<section>
			<h3>1. What cookies are</h3>
			<p>Cookies are small text files that a website places on a browser or device. Similar technologies may include local storage, session storage, pixels, tags, software development kits, and embedded third-party tools.</p>
			<p>These technologies can help a website remember choices, keep forms and security features working, understand how visitors use pages, and support optional features such as maps, video, social media, analytics, advertising, or partner tools.</p>
		</section>

		<section>
			<h3>2. How Limbe.Net uses cookies</h3>
			<p>Limbe.Net may use cookies and similar technologies to operate the site, secure forms, remember cookie preferences, support language and display choices, measure traffic, improve travel content, understand search and filter behavior, and support optional embedded services.</p>
			<p>We aim to use only cookies that are appropriate for the site feature involved. Non-essential cookies should be used only after you have given consent through the cookie preference widget or another clear consent control.</p>
		</section>

		<section>
			<h3>3. Strictly necessary cookies</h3>
			<p>Strictly necessary cookies are required for core site functions. These may include cookies needed for security, page navigation, form submissions, logged-in WordPress sessions, spam prevention, and storing your cookie consent choice.</p>
			<p>Because these cookies are necessary for the website to work, they cannot be switched off through the Limbe.Net cookie widget. You may still block them in your browser, but some parts of the site may not work correctly.</p>
		</section>

		<section>
			<h3>4. Analytics and performance cookies</h3>
			<p>Analytics and performance cookies help us understand visits, page performance, popular content, search terms, and how visitors move through Limbe.Net. This helps us improve destination pages, attraction pages, travel information, and partner discovery.</p>
			<p>These cookies are optional. If enabled, analytics providers may process technical information such as device type, browser type, approximate location, pages viewed, referring pages, dates, times, and interactions.</p>
		</section>

		<section>
			<h3>5. Preferences and functionality cookies</h3>
			<p>Preference and functionality cookies help remember choices such as language, display settings, saved form state, or optional interface preferences. They can make repeat visits easier and more consistent.</p>
			<p>These cookies are optional unless they are required for a specific service you request.</p>
		</section>

		<section>
			<h3>6. Marketing, social, and embedded media cookies</h3>
			<p>Marketing and embedded media cookies may be used by advertising partners, social platforms, video services, map providers, booking partners, or other embedded third-party features. These services may collect information under their own cookie and privacy notices.</p>
			<p>Examples may include embedded maps, videos, social sharing tools, retargeting pixels, sponsored listing tools, affiliate links, or partner booking features. These cookies are optional and should not be loaded before consent where consent is required.</p>
		</section>

		<section>
			<h3>7. Third-party cookies</h3>
			<p>Some services used on Limbe.Net may be provided by third parties. Third-party providers control their own cookies, retention periods, and privacy practices. Review their privacy and cookie notices before interacting with embedded tools or leaving Limbe.Net.</p>
			<p>Limbe.Net may update third-party tools over time as site features change. The cookie preference widget is intended to help manage optional categories at a high level.</p>
		</section>

		<section>
			<h3>8. How to manage your choices</h3>
			<p>When the cookie widget appears, you can allow all cookies, decline optional cookies, or accept selected categories. Your choice is stored in your browser so the site can remember it on future visits.</p>
			<p>You can also delete or block cookies through your browser settings. Browser controls usually allow you to clear existing cookies, block third-party cookies, or set site-specific permissions. If you clear cookies or use a different browser or device, you may need to set your Limbe.Net choices again.</p>
			<p><button class="lnet-cookie-policy-button" type="button" data-lnet-cookie-open>Open cookie preferences</button></p>
		</section>

		<section>
			<h3>9. Retention</h3>
			<p>Cookie retention periods vary by purpose and provider. Session cookies usually expire when you close your browser. Persistent cookies may remain until they expire, you delete them, or the relevant provider removes them.</p>
			<p>The Limbe.Net cookie consent choice may be stored for up to 180 days unless you clear it earlier.</p>
		</section>

		<section>
			<h3>10. Changes to this policy</h3>
			<p>We may update this Cookie Policy when site features, third-party tools, legal requirements, or operational practices change. Updated versions will be posted on this page with a revised last-updated date.</p>
		</section>

		<section>
			<h3>11. Contact</h3>
			<p>Questions about this Cookie Policy or cookie choices can be sent through the <a href="' . esc_url( $contact_url ) . '">Contact Us</a> page.</p>
		</section>
	</div>
</section>';
	}

	/**
	 * Import destinations.
	 *
	 * @return int Count.
	 */
	private function import_destinations() {
		$destinations = array(
			array( 'Limbe', 'South West', 'Coastal base for beaches, botanic gardens, wildlife education, and Mount Cameroon side trips.' ),
			array( 'Buea', 'South West', 'Mountain gateway with cooler weather, hiking access, and university-town energy.' ),
			array( 'Douala', 'Littoral', 'Cameroon city experiences, restaurants, nightlife, art, markets, and arrival logistics.' ),
			array( 'Yaounde', 'Centre', 'Cultural attractions, museums, day trips, and administrative city experiences.' ),
			array( 'Kribi', 'South Region', 'Beach escapes, coastal food, waterfalls, and relaxed weekend travel.' ),
			array( 'Foumban', 'West Region', 'Royal heritage, craft traditions, architecture, and cultural learning.' ),
			array( 'Bamenda', 'North West', 'Highland culture and scenic travel planning that requires current advisory checks.' ),
			array( 'North Cameroon', 'North Cameroon', 'Desert-edge landscapes, parks, and cultural routes that require careful current planning.' ),
			array( 'West Region', 'West Region', 'Highland towns, palaces, craft heritage, and road-trip itineraries.' ),
			array( 'South Region', 'South Region', 'Coastal, forest, and ecotourism routes around Kribi and beyond.' ),
			array( 'East Region', 'East Region', 'Forest reserves, wildlife conservation, and remote travel planning.' ),
		);

		$count = 0;
		foreach ( $destinations as $index => $destination ) {
			$title  = $destination[0];
			$region = $destination[1];
			$meta   = array(
				'destination_name'         => $title,
				'region'                   => $region,
				'overview'                 => $destination[2],
				'best_for'                 => 'Culture, nature, food, and responsible travel planning.',
				'travel_time_from_douala'  => 'Needs verification.',
				'travel_time_from_yaounde' => 'Needs verification.',
				'safety_notice'            => 'Check current travel advisory before planning this trip.',
				'advisory_level'           => 'check-before-travel',
				'top_attractions'          => 'Needs verification.',
				'where_to_stay'            => 'Use verified partner listings where available.',
				'how_to_get_there'         => 'Confirm current transport options before travel.',
				'last_verified_date'       => 'Needs verification.',
				'featured'                 => $index < 6 ? 'yes' : 'no',
			);

			if ( 'Limbe' === $title ) {
				$meta['featured_image'] = LIMBENET_CORE_URL . 'assets/images/limbe-city-featured.webp';
			}

			$post_id = $this->ensure_post(
				'destination',
				$title,
				$destination[2],
				$meta,
				array(
					'region'        => array( $region ),
					'safety_status' => array( 'Check current advisory before travel' ),
				)
			);

			if ( $post_id ) {
				$count++;
			}
		}

		return $count;
	}

	/**
	 * Import attractions.
	 *
	 * @return int Count.
	 */
	private function import_attractions() {
		$attractions = array(
			array( 'Limbe Botanic Garden', 'Limbe', 'South West', 'Eco-tourism', 'A historic garden and nature stop for slow walks, plant learning, and relaxed Limbe planning.' ),
			array( 'Limbe Wildlife Centre', 'Limbe', 'South West', 'Wildlife & Safari', 'A conservation-focused wildlife education stop that should be planned with current visitor information.' ),
			array( 'Bimbia Slave Trade Site', 'Limbe', 'South West', 'History', 'A sensitive heritage site connected to Cameroon history and remembrance.' ),
			array( 'Mount Cameroon', 'Buea', 'South West', 'Mountains & Hiking', 'A major hiking and volcanic landscape experience requiring guide planning and current safety checks.' ),
			array( 'Kribi Beaches', 'Kribi', 'South Region', 'Beaches', 'Coastal beach experiences around Kribi with food, relaxation, and weekend travel potential.' ),
			array( 'Lobe Falls', 'Kribi', 'South Region', 'Eco-tourism', 'A waterfall and coastal nature stop near Kribi that requires current access and ticket verification.' ),
			array( 'Foumban Royal Palace', 'Foumban', 'West Region', 'Culture & Heritage', 'A cultural heritage attraction connected to royal history, architecture, and craft traditions.' ),
			array( 'Dja Faunal Reserve', 'Somalomo', 'East Region', 'Wildlife & Safari', 'A major forest reserve experience that requires expert local planning and current access verification.' ),
			array( 'Waza National Park', 'Waza', 'North Cameroon', 'Wildlife & Safari', 'A northern park listing that requires current advisory checks and expert local guidance.' ),
			array( 'Korup National Park', 'Mundemba', 'South West', 'Eco-tourism', 'A rainforest and biodiversity destination requiring verified logistics and local guidance.' ),
			array( 'Mefou Primate Sanctuary', 'Yaounde', 'Centre', 'Wildlife & Safari', 'A primate conservation day-trip option near Yaounde with current visitor details to verify.' ),
			array( 'Ebogo Ecotourism Site', 'Ebogo', 'South Region', 'Eco-tourism', 'A river and nature-focused ecotourism stop that should be planned with local guidance.' ),
			array( 'Nkolandom Caves', 'Nkolandom', 'South Region', 'Family Trips', 'A cave and leisure attraction listing for family travel planning with ticket details to verify.' ),
			array( 'Rhumsiki', 'Rhumsiki', 'North Cameroon', 'Culture & Heritage', 'A dramatic northern landscape and cultural route requiring current advisory checks.' ),
			array( 'Douala city experiences', 'Douala', 'Littoral', 'Food & Nightlife', 'Urban food, market, art, music, and nightlife experiences for city-focused visitors.' ),
			array( 'Yaounde cultural attractions', 'Yaounde', 'Centre', 'Culture & Heritage', 'Museums, monuments, food, and cultural stops for visitors spending time in Yaounde.' ),
		);

		$count = 0;
		foreach ( $attractions as $index => $attraction ) {
			$title = $attraction[0];
			$city  = $attraction[1];
			$region = $attraction[2];
			$type  = $attraction[3];
			$desc  = $attraction[4];

			$post_id = $this->ensure_post(
				'attraction',
				$title,
				$desc,
				array(
					'attraction_subtitle'  => $type,
					'region'               => $region,
					'city'                 => $city,
					'attraction_type'      => $type,
					'short_description'    => $desc,
					'full_description'     => $desc . ' Limbe.Net keeps ticket prices unverified until a reliable source or official link is added.',
					'opening_hours'        => 'Needs verification.',
					'best_time_to_visit'   => 'Needs verification.',
					'recommended_duration' => 'Needs verification.',
					'ticket_required'      => 'unknown',
					'ticket_price_range'   => 'Price not yet verified.',
					'safety_notice'        => 'Check current travel advisory before planning this trip.',
					'advisory_level'       => 'check-before-travel',
					'accessibility_notes'  => 'Needs verification.',
					'family_friendly'      => 'yes',
					'nearby_hotels'        => 'See verified partner listings where available.',
					'nearby_restaurants'   => 'See verified partner listings where available.',
					'nearby_attractions'   => 'Needs verification.',
					'how_to_get_there'     => 'Confirm current transport routes, road conditions, and guide requirements before travel.',
					'last_verified_date'   => 'Needs verification.',
					'source_notes'         => 'Add official links where available.',
					'featured'             => $index < 8 ? 'yes' : 'no',
				),
				array(
					'region'          => array( $region ),
					'city'            => array( $city ),
					'attraction_type' => array( $type ),
					'travel_style'    => array( $type ),
					'safety_status'   => array( 'Check current advisory before travel' ),
				)
			);

			if ( $post_id ) {
				$count++;
			}
		}

		return $count;
	}

	/**
	 * Import editable travel info pages.
	 *
	 * @return int Count.
	 */
	private function import_travel_info() {
		$items = array(
			array(
				'title'    => 'Visa & eVisa',
				'subtitle' => 'Entry documents before you fly',
				'image'    => 'travel-info-visa-evisa.webp',
				'summary'  => 'Most visitors should confirm visa requirements before travel and use the official Cameroon eVisa portal for online applications when a visa is required.',
				'points'   => array(
					'Start with the official Cameroon eVisa portal and avoid unofficial social-media agents.',
					'Apply early enough for processing, airline checks, and any embassy follow-up.',
					'Carry your passport, approval record, accommodation or invitation details, and onward travel proof.',
					'Check yellow fever documentation and airline boarding rules before departure.',
				),
				'details'  => 'Cameroon operates an online eVisa process through the official evisacam.cm portal. Requirements can vary by nationality and trip purpose, so travelers should confirm their category before booking non-refundable travel. Keep digital and printed copies of the application confirmation and approval documents with the passport used for the application. Airlines and border officials may ask for proof of accommodation, invitation details, return or onward travel, and health documentation.',
				'safety'   => 'Entry rules can change quickly. Check the official eVisa portal, your airline, and your nearest Cameroon embassy or consulate before travel.',
				'level'    => 'check-before-travel',
				'sources'  => array(
					'Official Cameroon eVisa portal | https://www.evisacam.cm/',
					'UK FCDO Cameroon entry requirements | https://www.gov.uk/foreign-travel-advice/cameroon/entry-requirements',
				),
			),
			array(
				'title'    => 'Airports',
				'subtitle' => 'International gateways and arrivals',
				'image'    => 'travel-info-airports.webp',
				'summary'  => 'Douala and Yaounde are Cameroon\'s main international gateways, with Douala usually the practical arrival point for Limbe, Buea, and the coast.',
				'points'   => array(
					'Douala International Airport is usually the closest major gateway for Limbe and Buea.',
					'Yaounde Nsimalen International Airport is the main gateway for the capital and Centre Region.',
					'Confirm flight schedules, baggage rules, and domestic connections directly with airlines.',
					'Arrange trusted airport transfers in advance, especially for late arrivals.',
				),
				'details'  => 'Aeroports du Cameroun manages Cameroon airport information online, including airport pages and passenger information. For Limbe.Net travelers, Douala is the most common international arrival point for coastal trips, while Yaounde works well for capital-city, Centre Region, and onward domestic planning. Airport services, flight schedules, and domestic routes can change, so build extra time into transfers and avoid depending on tight same-day road connections after an international flight.',
				'safety'   => 'Use known taxis, hotel shuttles, or verified drivers for arrivals. Confirm transfer prices before departure from the airport.',
				'level'    => 'normal',
				'sources'  => array(
					'Aeroports du Cameroun | https://www.adcsa.aero/',
					'U.S. State Department Cameroon country information | https://travel.state.gov/content/travel/en/international-travel/International-Travel-Country-Information-Pages/Cameroon.html',
				),
			),
			array(
				'title'    => 'Getting around',
				'subtitle' => 'Roads, taxis, buses, and local transfers',
				'image'    => 'travel-info-getting-around.webp',
				'summary'  => 'Getting around Cameroon usually combines private transfers, city taxis, intercity buses, and local guidance, with road and security conditions checked before each route.',
				'points'   => array(
					'Use trusted drivers or established bus companies for intercity travel.',
					'Agree taxi fares before starting a trip and keep small cash for local rides.',
					'Avoid night road travel where possible and check current regional advisories.',
					'Carry ID and expect occasional checkpoints on longer routes.',
				),
				'details'  => 'Transport options vary by city and region. In Douala, Yaounde, Limbe, Buea, and Kribi, visitors commonly use taxis, hotel transfers, private drivers, or intercity buses. Road quality, traffic, weather, and security conditions can affect journey times. Longer routes should be planned with current local advice, especially during rainy periods or when crossing regions with active travel warnings. For attraction visits, use the attraction page, hotel, or verified partner listing to confirm practical pickup points and return arrangements.',
				'safety'   => 'Check current official travel advisories before road trips, avoid demonstrations, and use experienced local guidance for unfamiliar routes.',
				'level'    => 'check-before-travel',
				'sources'  => array(
					'UK FCDO Cameroon safety and security | https://www.gov.uk/foreign-travel-advice/cameroon/safety-and-security',
					'U.S. State Department Cameroon travel advisory | https://travel.state.gov/content/travel/en/traveladvisories/traveladvisories/cameroon-travel-advisory.html',
				),
			),
			array(
				'title'    => 'Money & payments',
				'subtitle' => 'Cash, cards, ATMs, and mobile money',
				'image'    => 'travel-info-money-payments.webp',
				'summary'  => 'Cameroon uses the Central African CFA franc. Cards and ATMs are useful in cities, but cash remains important for transport, markets, smaller restaurants, and many attractions.',
				'points'   => array(
					'Carry Central African CFA franc cash in small denominations for daily spending.',
					'Use ATMs in secure locations and keep backup payment options.',
					'Cards are more useful in larger hotels, supermarkets, and some city businesses than in small towns.',
					'Mobile money is common locally, but visitors may need a registered local SIM to use it fully.',
				),
				'details'  => 'Plan with a mix of cash and backup cards. Outside major hotels and some city businesses, cash is often the most reliable payment method. ATMs are easier to find in Douala, Yaounde, Limbe, Buea, Kribi, and other larger towns than in remote areas. Keep smaller notes for taxis, tips, snacks, markets, and entry fees. Mobile money is widely used by residents, but visitor access depends on local SIM registration, provider rules, and account setup.',
				'safety'   => 'Do not display large amounts of cash. Use secure ATMs, split backup cards from daily cash, and confirm prices before accepting services.',
				'level'    => 'normal',
				'sources'  => array(
					'U.S. State Department Cameroon country information | https://travel.state.gov/content/travel/en/international-travel/International-Travel-Country-Information-Pages/Cameroon.html',
					'UK FCDO Cameroon safety and security | https://www.gov.uk/foreign-travel-advice/cameroon/safety-and-security',
				),
			),
			array(
				'title'    => 'SIM cards & internet',
				'subtitle' => 'Mobile data for maps and messaging',
				'image'    => 'travel-info-sim-cards-internet.webp',
				'summary'  => 'An unlocked phone, passport, and local SIM can make Cameroon travel easier for maps, WhatsApp, hotel coordination, and mobile data.',
				'points'   => array(
					'Bring an unlocked phone that supports common African GSM/LTE bands.',
					'Expect identity registration when buying or activating a local SIM.',
					'Buy SIMs and data bundles from official shops or reputable agents.',
					'Download offline maps before remote trips because coverage can drop outside cities.',
				),
				'details'  => 'Mobile data is useful for ride coordination, WhatsApp, Google Maps, translation, and contacting guides. Major providers operate in Cameroon, but coverage and speed vary by city, coast, mountain areas, and remote routes. Buy from official shops or trusted outlets, keep the SIM registration receipt where possible, and ask the seller to confirm data-bundle activation before leaving. For travel outside major cities, carry offline maps and important contact numbers.',
				'safety'   => 'Use secure passwords and avoid sensitive financial activity on open public Wi-Fi. Keep a backup contact plan for remote areas.',
				'level'    => 'normal',
				'sources'  => array(
					'U.S. State Department Cameroon country information | https://travel.state.gov/content/travel/en/international-travel/International-Travel-Country-Information-Pages/Cameroon.html',
					'UK FCDO Cameroon safety and security | https://www.gov.uk/foreign-travel-advice/cameroon/safety-and-security',
				),
			),
			array(
				'title'    => 'Safety & travel advisories',
				'subtitle' => 'Check official guidance before every trip',
				'image'    => 'travel-info-safety-travel-advisories.webp',
				'summary'  => 'Cameroon travel planning should always include current official advisories because conditions vary significantly by region.',
				'points'   => array(
					'Check official advisories on the day you plan and again before departure.',
					'Some governments advise against travel to specific Cameroon regions because of conflict, crime, terrorism, or kidnapping risks.',
					'Use experienced local guidance and verified transport for trips outside major city cores.',
					'Avoid demonstrations, political gatherings, and night road travel where possible.',
				),
				'details'  => 'Safety conditions in Cameroon are not uniform. Major city travel, coastal leisure, highland routes, northern parks, and border areas can carry very different risk levels. Official advisories from the traveler\'s own government should guide route decisions. Limbe.Net pages display safety notices as planning prompts, not guarantees. Travelers should also monitor local news, hotel advice, guide advice, and embassy alerts during the trip.',
				'safety'   => 'Official advisories currently identify elevated risks in several regions. Do not treat older blog posts, social media comments, or archived guides as current safety guidance.',
				'level'    => 'high-risk',
				'sources'  => array(
					'U.S. State Department Cameroon travel advisory | https://travel.state.gov/content/travel/en/traveladvisories/traveladvisories/cameroon-travel-advisory.html',
					'UK FCDO Cameroon travel advice | https://www.gov.uk/foreign-travel-advice/cameroon',
				),
			),
			array(
				'title'    => 'Health & packing',
				'subtitle' => 'Vaccines, malaria prevention, and practical kit',
				'image'    => 'travel-info-health-packing.webp',
				'summary'  => 'Cameroon travelers should review vaccines, malaria prevention, yellow fever documentation, food and water precautions, and a practical tropical travel kit before departure.',
				'points'   => array(
					'Consult a travel health professional well before departure.',
					'Check yellow fever certificate requirements and routine vaccine updates.',
					'Ask about malaria prevention and bring insect repellent, long sleeves, and sleep precautions.',
					'Pack prescription medicines, basic first aid, sun protection, rain protection, and travel insurance details.',
				),
				'details'  => 'CDC Travelers Health guidance for Cameroon includes destination-specific vaccine and malaria information. Travelers should review this with a clinician, especially for children, pregnant travelers, older travelers, and anyone with existing health conditions. Pack enough prescription medication for the full trip plus delays, and carry it in original packaging. For coastal and rainforest routes, prepare for heat, rain, insects, and muddy paths. Use bottled or treated water when needed and choose food vendors carefully.',
				'safety'   => 'This is not medical advice. Use CDC guidance and a qualified travel health professional for personal recommendations.',
				'level'    => 'check-before-travel',
				'sources'  => array(
					'CDC Travelers Health Cameroon | https://wwwnc.cdc.gov/travel/destinations/traveler/none/cameroon',
					'UK FCDO Cameroon health | https://www.gov.uk/foreign-travel-advice/cameroon/health',
				),
			),
			array(
				'title'    => 'Best time to visit',
				'subtitle' => 'Seasonal planning by region',
				'image'    => 'travel-info-best-time-to-visit.webp',
				'summary'  => 'The best time to visit Cameroon depends on region and activity, but drier months are usually easier for road trips, hiking, and beach planning.',
				'points'   => array(
					'For the coast and Mount Cameroon routes, check local rain and road conditions before travel.',
					'Drier months are often easier for hiking, road transfers, and multi-city itineraries.',
					'Rainy periods can be lush and beautiful but can affect roads, trails, and beach conditions.',
					'Northern routes can be affected by heat, dust, and security advisories, so plan carefully.',
				),
				'details'  => 'Cameroon has coastal, highland, forest, savanna, and northern climate zones, so a single best month does not fit every itinerary. Limbe, Kribi, and the coast are humid and can receive heavy rain. Buea and Mount Cameroon can be cooler and cloudier, with mountain weather changing quickly. Yaounde and Douala are practical year-round city stops, but traffic, rain, and heat should shape daily plans. For multi-region trips, combine climate planning with current safety guidance.',
				'safety'   => 'Weather can affect roads, trails, and sea conditions. Confirm local conditions before hikes, waterfalls, and long road trips.',
				'level'    => 'normal',
				'sources'  => array(
					'World Bank Climate Change Knowledge Portal Cameroon | https://climateknowledgeportal.worldbank.org/country/cameroon/climate-data-historical',
					'UK FCDO Cameroon travel advice | https://www.gov.uk/foreign-travel-advice/cameroon',
				),
			),
			array(
				'title'    => 'Responsible travel',
				'subtitle' => 'Respect communities, wildlife, and heritage',
				'image'    => 'travel-info-responsible-travel.webp',
				'summary'  => 'Responsible Cameroon travel means using local services fairly, respecting cultural and historic sites, reducing waste, and protecting wildlife.',
				'points'   => array(
					'Ask permission before photographing people, ceremonies, homes, or sensitive heritage sites.',
					'Use local guides and locally owned businesses where possible.',
					'Do not feed, touch, or disturb wildlife; follow sanctuary and park rules.',
					'Carry out plastic waste and respect memorial sites such as Bimbia with a quiet tone.',
				),
				'details'  => 'Cameroon travel is strongest when visitors support local communities and respect place-specific rules. Pay official entry or guide fees where required, agree prices clearly, tip fairly when service is good, and avoid bargaining in ways that undermine livelihoods. At cultural and historical places, follow guide instructions and keep photography respectful. At beaches, waterfalls, forests, and wildlife sites, stay on approved routes, avoid litter, and never pressure guides or staff to break conservation rules.',
				'safety'   => 'Responsible travel also protects visitors: local rules, community advice, and conservation guidance often exist because conditions can change quickly.',
				'level'    => 'normal',
				'sources'  => array(
					'UN Tourism Global Code of Ethics for Tourism | https://www.unwto.org/global-code-of-ethics-for-tourism',
					'CDC Travelers Health Cameroon | https://wwwnc.cdc.gov/travel/destinations/traveler/none/cameroon',
				),
			),
		);

		$count = 0;
		foreach ( $items as $index => $item ) {
			$meta = array(
				'travel_info_subtitle' => $item['subtitle'],
				'summary'              => $item['summary'],
				'featured_image'       => LIMBENET_CORE_URL . 'assets/images/' . $item['image'],
				'key_points'           => implode( "\n", $item['points'] ),
				'details'              => $item['details'],
				'official_links'       => implode( "\n", $item['sources'] ),
				'safety_notice'        => $item['safety'],
				'advisory_level'       => $item['level'],
				'last_verified_date'   => '2026-07-07',
				'source_notes'         => 'Seeded with official and public travel guidance. Re-check source links before publishing major updates.',
				'featured'             => 'yes',
			);

			$post_id = $this->ensure_post(
				'travel_info',
				$item['title'],
				$item['summary'],
				$meta,
				array()
			);

			if ( $post_id ) {
				wp_update_post(
					array(
						'ID'         => $post_id,
						'menu_order' => $index,
					)
				);
				$count++;
			}
		}

		return $count;
	}

	/**
	 * Import sample itineraries.
	 *
	 * @return int Count.
	 */
	private function import_itineraries() {
		$items = array(
			array( 'Three Days in Limbe and Buea', '3 days', 'Limbe', 'Buea' ),
			array( 'Cameroon Coastal Weekend', '2 days', 'Douala', 'Kribi' ),
			array( 'Culture and Wildlife Highlights', '5 days', 'Yaounde', 'Foumban' ),
		);

		$count = 0;
		foreach ( $items as $index => $item ) {
			$post_id = $this->ensure_post(
				'itinerary',
				$item[0],
				'Sample itinerary framework. Replace with verified travel times, partners, and official links before publishing as final guidance.',
				array(
					'duration'                => $item[1],
					'starting_city'           => $item[2],
					'ending_city'             => $item[3],
					'budget_range'            => 'Needs verification.',
					'difficulty'              => 'Moderate',
					'best_for'                => 'Visitors who want structured Cameroon planning.',
					'itinerary_days_repeater' => "Day 1: Needs verification.\nDay 2: Needs verification.\nDay 3: Needs verification.",
					'included_attractions'    => 'Needs verification.',
					'recommended_partners'    => 'Use verified partner listings where available.',
					'safety_notes'            => 'Check current travel advisory before planning this trip.',
					'sponsored_content'       => 'no',
					'featured'                => $index < 3 ? 'yes' : 'no',
				),
				array(
					'travel_style'  => array( 'Weekend Trips' ),
					'difficulty'    => array( 'Moderate' ),
					'budget_range'  => array( 'Mid-range' ),
					'safety_status' => array( 'Check current advisory before travel' ),
				)
			);

			if ( $post_id ) {
				$count++;
			}
		}

		return $count;
	}

	/**
	 * Import sample partners.
	 *
	 * @return int Count.
	 */
	private function import_partners() {
		$items = array(
			array( 'Sample Verified Tour Guide', 'tour-guide', 'Limbe', 'South West' ),
			array( 'Sample Coastal Transport Partner', 'transport', 'Douala', 'Littoral' ),
			array( 'Sample Heritage Photographer', 'photographer', 'Yaounde', 'Centre' ),
		);

		$count = 0;
		foreach ( $items as $index => $item ) {
			$post_id = $this->ensure_post(
				'partner',
				$item[0],
				'Sample partner listing for layout and workflow testing. Replace with verified business details before public launch.',
				array(
					'business_name'       => $item[0],
					'business_type'       => $item[1],
					'city'                => $item[2],
					'region'              => $item[3],
					'description'         => 'Sample partner listing for layout and workflow testing.',
					'phone'               => '',
					'whatsapp'            => '',
					'email'               => '',
					'website'             => '',
					'booking_url'         => '',
					'price_range'         => 'Needs verification.',
					'verified_partner'    => $index < 1 ? 'yes' : 'no',
					'paid_plan'           => $index < 1 ? 'verified' : 'free',
					'listing_expiry_date' => '',
					'sponsored_content'   => 'no',
					'featured'            => $index < 1 ? 'yes' : 'no',
				),
				array(
					'region'       => array( $item[3] ),
					'city'         => array( $item[2] ),
					'partner_type' => array( ucwords( str_replace( '-', ' ', $item[1] ) ) ),
					'budget_range' => array( 'Mid-range' ),
				)
			);

			if ( $post_id ) {
				$count++;
			}
		}

		return $count;
	}

	/**
	 * Import sample deals.
	 *
	 * @return int Count.
	 */
	private function import_deals() {
		$items = array(
			array( 'Sample Partner Welcome Deal', 'tour', 'Sample Verified Tour Guide' ),
			array( 'Sample Weekend Transport Offer', 'transport', 'Sample Coastal Transport Partner' ),
		);

		$count = 0;
		foreach ( $items as $item ) {
			$post_id = $this->ensure_post(
				'deal',
				$item[0],
				'Sample deal listing. Replace terms, dates, and booking link after partner verification.',
				array(
					'deal_title'        => $item[0],
					'partner'           => $item[2],
					'deal_type'         => $item[1],
					'description'       => 'Sample deal listing. Replace after partner verification.',
					'discount_text'     => 'Discount details not yet verified.',
					'start_date'        => '',
					'end_date'          => '',
					'booking_url'       => '',
					'coupon_code'       => '',
					'terms'             => 'Terms need verification.',
					'sponsored_content' => 'yes',
					'featured'          => 'yes',
				),
				array(
					'budget_range' => array( 'Mid-range' ),
				)
			);

			if ( $post_id ) {
				$count++;
			}
		}

		return $count;
	}

	/**
	 * Import sample events.
	 *
	 * @return int Count.
	 */
	private function import_events() {
		$post_id = $this->ensure_post(
			'event',
			'Sample Cultural Event Listing',
			'Sample event listing. Add verified dates, ticket links, organizer contact, and venue details before launch.',
			array(
				'event_name'        => 'Sample Cultural Event Listing',
				'event_type'        => 'Culture',
				'city'              => 'Limbe',
				'region'            => 'South West',
				'start_date'        => '',
				'end_date'          => '',
				'venue'             => 'Needs verification.',
				'description'       => 'Sample event listing for workflow testing.',
				'ticket_required'   => 'unknown',
				'ticket_url'        => '',
				'organizer_contact' => 'Needs verification.',
				'featured'          => 'yes',
			),
			array(
				'region' => array( 'South West' ),
				'city'   => array( 'Limbe' ),
			)
		);

		return $post_id ? 1 : 0;
	}

	/**
	 * Ensure a term exists.
	 *
	 * @param string $taxonomy Taxonomy.
	 * @param string $name Term name.
	 * @return int Term ID or zero.
	 */
	private function ensure_term( $taxonomy, $name ) {
		$term = term_exists( $name, $taxonomy );
		if ( $term && ! is_wp_error( $term ) ) {
			return (int) $term['term_id'];
		}

		$result = wp_insert_term( $name, $taxonomy );
		if ( is_wp_error( $result ) ) {
			return 0;
		}

		return (int) $result['term_id'];
	}

	/**
	 * Ensure page exists.
	 *
	 * @param string $title Page title.
	 * @param string $slug Page slug.
	 * @param string $content Page content.
	 * @param bool   $overwrite Whether to overwrite existing page content.
	 * @return int Post ID.
	 */
	private function ensure_page( $title, $slug, $content, $overwrite = true ) {
		$page = get_page_by_path( $slug, OBJECT, 'page' );
		if ( $page ) {
			if ( $overwrite ) {
				wp_update_post(
					array(
						'ID'           => $page->ID,
						'post_title'   => $title,
						'post_content' => $content,
					)
				);
			}
			return (int) $page->ID;
		}

		return (int) wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => $title,
				'post_name'    => $slug,
				'post_content' => $content,
			)
		);
	}

	/**
	 * Ensure a custom post exists and update fields.
	 *
	 * @param string $post_type Post type.
	 * @param string $title Title.
	 * @param string $content Content.
	 * @param array  $meta Meta values.
	 * @param array  $terms Taxonomy terms.
	 * @return int Post ID.
	 */
	private function ensure_post( $post_type, $title, $content, $meta, $terms = array() ) {
		$existing = get_posts(
			array(
				'post_type'      => $post_type,
				'title'          => $title,
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
			)
		);

		if ( $existing ) {
			$post_id = (int) $existing[0];
			wp_update_post(
				array(
					'ID'           => $post_id,
					'post_title'   => $title,
					'post_content' => $content,
				)
			);
		} else {
			$post_id = (int) wp_insert_post(
				array(
					'post_type'    => $post_type,
					'post_status'  => 'publish',
					'post_title'   => $title,
					'post_content' => $content,
					'post_excerpt' => wp_trim_words( $content, 28, '' ),
				)
			);
		}

		if ( ! $post_id ) {
			return 0;
		}

		foreach ( $meta as $key => $value ) {
			update_post_meta( $post_id, $key, $value );
		}

		foreach ( $terms as $taxonomy => $names ) {
			wp_set_object_terms( $post_id, $names, $taxonomy, false );
		}

		return $post_id;
	}
}
