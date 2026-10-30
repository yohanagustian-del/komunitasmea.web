<?php
/**
 * Halaman. Halaman Elementor dirender polos (hanya the_content()) supaya
 * landing page lama tampil seperti dibuat. Template Elementor Canvas dan
 * Full Width tetap ditangani Elementor sendiri.
 *
 * @package komunitasmea
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	if ( km_is_elementor_page() ) :
		?>
		<main id="main" class="site-main site-main--builder">
			<?php the_content(); ?>
		</main>
		<?php
	else :
		?>
		<main id="main" class="site-main">
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'wrap page-article' ); ?>>
				<header class="entry-header">
					<h1 class="entry-title"><?php the_title(); ?></h1>
				</header>
				<div class="prose entry-content">
					<?php
					the_content();
					wp_link_pages(
						array(
							'before' => '<nav class="page-links" aria-label="Halaman">Halaman: ',
							'after'  => '</nav>',
						)
					);
					?>
				</div>
				<?php
				if ( comments_open() || get_comments_number() ) {
					comments_template();
				}
				?>
			</article>
		</main>
		<?php
	endif;
endwhile;

get_footer();
