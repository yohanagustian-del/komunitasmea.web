<?php
/**
 * Daftar kartu artikel + paginasi untuk query utama.
 *
 * @package komunitasmea
 */

defined( 'ABSPATH' ) || exit;

if ( have_posts() ) :
	?>
	<ul class="cards">
		<?php
		while ( have_posts() ) :
			the_post();
			echo '<li>';
			get_template_part( 'template-parts/post-card' );
			echo '</li>';
		endwhile;
		?>
	</ul>
	<?php
	the_posts_pagination(
		array(
			'mid_size'           => 1,
			'prev_text'          => '← Sebelumnya',
			'next_text'          => 'Berikutnya →',
			'screen_reader_text' => 'Navigasi halaman',
			'aria_label'         => 'Halaman artikel',
		)
	);
else :
	?>
	<p class="empty">Belum ada artikel di sini.</p>
	<?php
endif;
