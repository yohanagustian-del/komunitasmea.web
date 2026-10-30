<?php
/**
 * FAQ homepage (§3.8). Schema FAQPage dari data yang sama ada di inc/schema.php.
 * Jawaban yang belum ada tidak tampil ke pengunjung; admin melihat placeholder.
 *
 * @package komunitasmea
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="section wrap faq" id="faq" aria-labelledby="faq-title">
	<h2 id="faq-title">Pertanyaan yang sering ditanyakan</h2>
	<?php
	foreach ( km_faq_items() as $km_item ) :
		$km_answer = trim( $km_item['a'] );
		if ( '' === $km_answer && ! km_placeholder() ) {
			continue;
		}
		?>
		<details>
			<summary><?php echo esc_html( $km_item['q'] ); ?></summary>
			<p><?php echo '' !== $km_answer ? esc_html( $km_answer ) : km_placeholder(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
		</details>
	<?php endforeach; ?>
</section>
