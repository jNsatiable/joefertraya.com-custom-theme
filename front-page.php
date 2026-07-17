<?php
/**
 * Front page — single-screen hero replicating the Elementor original (post 713).
 */

get_header();

$jt_skills = array(
	'CUSTOMER DELIGHT',
	'SOFTWARE DEVELOPMENT',
	'WEB DEVELOPMENT',
	'DESIGN',
	'AUTOMATION',
	'TECH SUPPORT',
	'HEALTHCARE',
	'PROJECT MANAGEMENT',
	'DATA ANALYSIS',
	'PHOTOGRAPHY',
	'PHOTO RETOUCHING',
	'MULTIMEDIA EDITING',
	'MUSIC',
	'AI PROMPT ENGINEERING',
);
?>

<section class="home-hero" data-skills="<?php echo esc_attr( wp_json_encode( $jt_skills ) ); ?>">
	<canvas class="home-hero__canvas" aria-hidden="true"></canvas>
	<div class="container home-hero__container">
	<div class="home-hero__card">
		<h1 class="home-hero__title">
			Hi! I'm <span class="home-hero__accent">J</span>
			<span class="home-hero__wave" aria-hidden="true">&#x1F44B;</span>
		</h1>
		<h3 class="home-hero__sub">Multifaceted VA with skills in</h3>
		<p class="home-hero__rotator" aria-label="<?php echo esc_attr( implode( ', ', array_map( 'ucwords', array_map( 'strtolower', $jt_skills ) ) ) ); ?>">
			<span class="home-hero__rotator-text"></span><span class="home-hero__cursor" aria-hidden="true">|</span>
		</p>
		<p class="home-hero__tagline">Empowering businesses with exceptional creativity and high technicality &mdash; wearing multiple hats at a time.</p>
		<div class="home-hero__actions">
			<a class="home-hero__btn" href="<?php echo esc_url( home_url( '/about/' ) ); ?>">About Me</a>
			<a class="home-hero__btn" href="<?php echo esc_url( home_url( '/portfolio/' ) ); ?>">My Work</a>
		</div>
	</div>
	</div>
</section>

<?php
get_footer();
