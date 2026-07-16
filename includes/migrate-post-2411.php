<?php
/**
 * One-time content migration for post 2411 (the Google Podcasts article).
 *
 * The post was built with Elementor; its real content lived in
 * _elementor_data JSON and only rendered while the Elementor plugin was
 * active. This rewrites post_content with clean theme-native HTML (same
 * text, image, lists, and the two self-hosted videos) so the post has no
 * plugin dependency at all.
 *
 * Pattern: flag-guarded init hook (fires once, no login/form/REST needed) —
 * same seed-content approach proven on Rentl. _elementor_data is kept in
 * the DB as a dormant backup; _elementor_edit_mode is removed so the
 * theme's single.php prints the post title again.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function jt_migrate_post_2411_once() {
	if ( get_option( 'jt_post_2411_migrated' ) ) {
		return;
	}

	$post = get_post( 2411 );
	if ( ! $post ) {
		update_option( 'jt_post_2411_migrated', 'post-not-found' );
		return;
	}

	$content = <<<'HTML'
<figure class="post-hero-image">
<a href="https://pixabay.com/photos/audio-concert-mic-microphone-music-2941753/" target="_blank" rel="noopener"><img src="https://joefertraya.com/wp-content/uploads/2024/03/audio-2941753_1280.jpg" alt="Microphone at a concert"></a>
</figure>

<p>In September 2023, Google announced that it would <a href="https://techcrunch.com/2023/09/26/google-podcasts-to-shut-down-in-2024-with-listeners-migrated-to-youtube-music/" target="_blank" rel="noopener">shut down Google Podcasts</a> in 2024 to integrate it with YouTube Music. Users in the U.S. have been warned to migrate their subscriptions to <a href="https://music.youtube.com/" target="_blank" rel="noopener">YouTube Music</a> before April 2. After this date, streaming will no longer be possible, but users have until July to complete the migration process.</p>

<h4>The Problem</h4>

<p>If you're like me, a Google Podcast user who:</p>
<ul>
<li>Does not want to use YouTube Music; or</li>
<li>Found it difficult to find resources for an easy migration (case in point: YouTube Music's <a href="https://blog.youtube/news-and-events/migrating-your-podcasts/" target="_blank" rel="noopener">official migration tool does NOT work</a>)</li>
</ul>
<p>Then you're in luck because we will do just that — migrate our precious podcast subscriptions somewhere else in a straight-to-the-point, easy tutorial.</p>

<h4>How to Migrate Your Google Podcasts Subscriptions</h4>

<p>Foreword: We will be using <a href="https://play.google.com/store/apps/details?id=com.bambuna.podcastaddict&amp;hl=en&amp;gl=US&amp;pli=1" target="_blank" rel="noopener">Podcast Addict</a> in the following guide. It's not mandatory to use it; just make sure that your preferred podcast app or service supports the import of <a href="https://en.wikipedia.org/wiki/OPML" target="_blank" rel="noopener">OPML</a> files.</p>

<h5>Step 1: Export Your Subscriptions Using Google Takeout</h5>

<p>But what is <a href="https://www.androidpolice.com/google-takeout-guide/" target="_blank" rel="noopener">Google Takeout</a>, you ask? Essentially, it's a tool that allows you to download or migrate ALL of your Google-related data. This guide only focuses on how to use it for Google Podcasts.</p>
<ul>
<li>Go to <a href="https://takeout.google.com/" target="_blank" rel="noopener">your Google Takeout page</a>
<ul>
<li>Click "Deselect all" to avoid backing up all your Google data, which can be extensive and time-consuming</li>
<li>Tick Google Podcasts</li>
<li>Click <i>Next Step</i></li>
<li>Choose "Send download link via email" or "Add to Drive"</li>
<li>Click <i>Create export</i>. At this point you might get prompted to enter your Google account password.</li>
<li>For further clarification, please refer to the video below.</li>
</ul>
</li>
</ul>

<video controls preload="metadata" src="https://joefertraya.com/wp-content/uploads/2024/03/google-takeout.mp4"></video>

<ul>
<li>You will receive an email whether you chose that option or the "Add to Drive" one, informing you that the backup file — which is in .zip format — is ready.</li>
<li>Download and <strong>extract</strong> (this is important) the .zip file to somewhere convenient for later access by your android phone. <em>Tip: Use your Google Drive's root location. You may also just email the files to yourself. The important file here is the Subscriptions.opml</em></li>
</ul>

<h5>Step 2: Import the OPML File to Your Podcast App</h5>

<ul>
<li>Download <a href="https://play.google.com/store/apps/details?id=com.bambuna.podcastaddict&amp;hl=en&amp;gl=US&amp;pli=1" target="_blank" rel="noopener">Podcast Addict</a> if you haven't already.</li>
<li>Navigate to "Restore" (Podcast Addict's import feature):
<ul>
<li>Hamburger Icon -&gt; Gear Icon -&gt; Backup/Restore -&gt; Restore</li>
<li>Find and click on Subscriptions.opml file</li>
</ul>
</li>
<li>Specify podcasts to restore by ticking on their designated check marks or the top check mark if you want to restore all.</li>
<li>Enjoy! But if you can't because you're stuck, refer to the video below.</li>
</ul>

<video controls preload="metadata" src="https://joefertraya.com/wp-content/uploads/2024/03/google-podcasts-migration.mp4"></video>
HTML;

	// wp_update_post() (via wp_insert_post) expects slashed data — same
	// wp_unslash() corruption class as the meta gotcha in the Framework doc.
	wp_update_post(
		wp_slash(
			array(
				'ID'           => 2411,
				'post_content' => $content,
			)
		)
	);

	delete_post_meta( 2411, '_elementor_edit_mode' );
	update_post_meta( 2411, '_jt_migrated_from_elementor', gmdate( 'Y-m-d' ) );
	update_option( 'jt_post_2411_migrated', 'done' );
}
add_action( 'init', 'jt_migrate_post_2411_once' );
