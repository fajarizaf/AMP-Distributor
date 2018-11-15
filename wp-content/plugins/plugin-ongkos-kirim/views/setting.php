<div class="wrap pok-wrapper">
	<h1 class="wp-heading-inline">Plugin Ongkos Kirim</h1>
	<hr class="wp-header-end">
	<nav class="nav-tab-wrapper woo-nav-tab-wrapper">
		<?php foreach ( $tabs as $key => $value ) : ?>
			<?php
			if ( 'log' === $key && 'log' !== $tab ) {
				continue;
			}
			?>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=pok_setting&tab=' . $key ) ); ?>" class="nav-tab <?php echo $tab === $key ? 'nav-tab-active' : ''; ?>"><?php echo esc_html( $value['label'] ); ?></a>
		<?php endforeach; ?>
	</nav>
	<div class="pok-setting-content">
		<?php
		if ( isset( $tabs[ $tab ]['callback'] ) ) {
			call_user_func( $tabs[ $tab ]['callback'] );
		}
		?>
	</div>
</div>
