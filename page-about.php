<?php
/**
 * About page — replicates Elementor post 6.
 */

get_header();
?>

<section class="page-section about-intro">
	<div class="container about-intro__grid">
		<div class="about-intro__text">
			<h1 class="page-title">About Me</h1>
			<p>Hello! I'm <strong>Joefer Traya</strong>. My drive for efficiency compels me to ask that you just call me J. A registered nurse who got bit by the coding bug later than most, I ventured into the scary but exhilarating world of virtual freelancing.</p>
			<p>Since then, I have collaborated with amazing individuals and businesses, consistently fueling my natural passion for learning and self-improvement. From administrative assistance to tech support, data entry to data analytics, web development to automations, and photo editing to AI prompting &mdash; I thoroughly enjoy each role &#x1F609;</p>
			<p>I recognize that business success hinges on more than just technical expertise&mdash;interpersonal connections matter most. That is why it's crucial to know each other before initiating any work. If you believe we'd work well together, scroll down further to know more about my quirks! I'm a shameless firecracker of dad jokes&mdash;consider yourself warned!</p>
			<p>Directly below is my resume, too, if you prefer that &#x1F642;</p>
			<a class="jt-btn" href="<?php echo esc_url( home_url( '/wp-content/uploads/2026/03/JOEFER_TRAYA_RESUME_v4.6.pdf' ) ); ?>" target="_blank" rel="noopener">RESUME</a>
		</div>
		<div class="about-intro__photo">
			<img src="<?php echo esc_url( home_url( '/wp-content/uploads/2024/01/P6280453-Edit-2-scaled.jpg' ) ); ?>" alt="Joefer Traya" loading="lazy">
		</div>
	</div>
</section>

<section class="page-section about-quirks">
	<div class="container">
		<h3 class="section-title">More J Stuff</h3>
		<div class="jt-toggles">
			<details class="jt-toggle">
				<summary>My Myers-Briggs Type Indicator (MBTI)</summary>
				<div class="jt-toggle__body"><p>INFJ. No, wait, maybe INTJ? Though, if <a href="<?php echo esc_url( home_url( '/wp-content/uploads/2024/01/INFJ.png' ) ); ?>" target="_blank" rel="noopener">this report</a> were to be believed, I apparently embody <a href="https://www.16personalities.com/personality-types" target="_blank" rel="noopener">8 out of the 16 personalities</a>, hmm.</p></div>
			</details>
			<details class="jt-toggle">
				<summary>My Love Language</summary>
				<div class="jt-toggle__body"><p><strong>Acts of Service</strong>. The quality of my work speaks for me (or at least I hope it does). More about the <a href="https://5lovelanguages.com/learn" target="_blank" rel="noopener">5 Love Languages</a> by Gary Chapman.</p></div>
			</details>
			<details class="jt-toggle">
				<summary>"Can you perform [insert any skill / task]?"</summary>
				<div class="jt-toggle__body"><p>Absolutely! I have full confidence in my ability to learn just about anything (except, maybe, rocket science XD). The feasibility depends on time constraints, really. How soon do you need the output?</p></div>
			</details>
			<details class="jt-toggle">
				<summary>My Super Power</summary>
				<div class="jt-toggle__body"><p>Can read and spell words backward. Oh, and I seem to excel in bringing out the best in people, too&mdash;always on the lookout for improvement opportunities for the people around me.</p></div>
			</details>
			<details class="jt-toggle">
				<summary>My Kryptonite</summary>
				<div class="jt-toggle__body"><p>As someone who always has something good to say about other people, I struggle with giving out constructive criticism. Though if <a href="https://5lovelanguages.com/learn" target="_blank" rel="noopener">words of affirmation</a> fuel you better than tough love, that's a plus, no?</p></div>
			</details>
			<details class="jt-toggle">
				<summary>My Current interests</summary>
				<div class="jt-toggle__body"><p>With my recently acquired data analytics skills, I am looking at breaking into the fields of digital sales and marketing, lead generation, social media management and the likes. Very interested in how consumer psychology works.</p></div>
			</details>
		</div>
	</div>
</section>

<section class="page-section jt-cta">
	<div class="container">
		<h3 class="jt-cta__title">Ready to elevate your business to the next level?</h3>
		<p class="jt-cta__sub">But first, how may I help you?<br>Let's discuss your project and make it happen!</p>
		<a class="jt-btn jt-btn--cta" href="<?php echo esc_url( home_url( '/contact-form/' ) ); ?>">LET'S WORK TOGETHER</a>
	</div>
</section>

<?php
get_footer();
