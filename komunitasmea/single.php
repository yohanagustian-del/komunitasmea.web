<?php
/**
 * Artikel. CTA MEA Router: kartu ringkas disisipkan plugin setelah paragraf ke-3,
 * kartu lengkap di slot setelah konten (do_action 'mea_router_slot').
 *
 * @package komunitasmea
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="site-main">
	<?php
	while ( have_posts() ) :
		the_post();
		$km_cat  = km_primary_category();
		$km_path = km_category_path( $km_cat );
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'wrap entry' ); ?>>
			<header class="entry-header">
				<?php
				if ( function_exists( 'rank_math_the_breadcrumbs' ) ) {
					rank_math_the_breadcrumbs();
				}
				?>
				<?php if ( $km_cat ) : ?>
					<p class="entry-cat"><a class="<?php echo 'kreator' === $km_path ? 'is-k' : ''; ?>" href="<?php echo esc_url( get_category_link( $km_cat ) ); ?>"><?php echo esc_html( $km_cat->name ); ?></a></p>
				<?php endif; ?>
				<h1 class="entry-title"><?php the_title(); ?></h1>
				<p class="entry-meta">Oleh <?php the_author(); ?> · <?php echo km_entry_date(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
			</header>

			<?php if ( has_post_thumbnail() ) : ?>
				<figure class="entry-thumb">
					<?php
					the_post_thumbnail(
						'large',
						array(
							'loading'       => 'eager',
							'fetchpriority' => 'high',
							'sizes'         => '(max-width: 820px) calc(100vw - 2.5rem), 760px',
						)
					);
					?>
				</figure>
			<?php endif; ?>

			<div class="prose entry-content">
				<?php
				the_content();
				wp_link_pages(
					array(
						'before' => '<nav class="page-links" aria-label="Halaman artikel">Halaman: ',
						'after'  => '</nav>',
					)
				);
				?>
			</div>

			<?php do_action( 'mea_router_slot', 'end' ); ?>

			<?php
			$km_tags = get_the_tags();
			if ( $km_tags ) :
				?>
				<ul class="entry-tags" aria-label="Tag">
					<?php foreach ( $km_tags as $km_tag ) : ?>
						<li><a href="<?php echo esc_url( get_tag_link( $km_tag ) ); ?>">#<?php echo esc_html( $km_tag->name ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<?php
			if ( $km_cat ) :
				$km_related = get_posts(
					array(
						'cat'                 => $km_cat->term_id,
						'post__not_in'        => array( get_the_ID() ),
						'posts_per_page'      => 3,
						'ignore_sticky_posts' => true,
						'no_found_rows'       => true,
						'suppress_filters'    => false,
					)
				);
				if ( $km_related ) :
					?>
					<section class="related" aria-labelledby="related-title">
						<h2 id="related-title">Baca juga</h2>
						<ul class="cards">
							<?php
							foreach ( $km_related as $post ) : // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
								setup_postdata( $post );
								echo '<li>';
								get_template_part( 'template-parts/post-card', null, array( 'heading' => 'h3' ) );
								echo '</li>';
							endforeach;
							wp_reset_postdata();
							?>
						</ul>
					</section>
					<?php
				endif;
			endif;

			if ( comments_open() || get_comments_number() ) {
				comments_template();
			}
			?>
		</article>
	<?php endwhile; ?>
</main>
<?php
get_footer();
