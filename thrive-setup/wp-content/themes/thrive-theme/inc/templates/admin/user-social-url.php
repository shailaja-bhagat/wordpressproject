<h3> <?php echo __( 'Thrive Social URL Settings', THEME_DOMAIN ); ?> </h3>
<table class="form-table">
	<?php $social_urls = get_the_author_meta( THRIVE_SOCIAL_OPTION_NAME, $user->ID ); ?>
	<?php foreach ( Thrive_Defaults::social_labels() as $key => $value ) { ?>
		<tr>
			<th><label for=<?php echo $key ?>> <?php echo $value ?></label></th>
			<td>
				<input type="text" name="<?php echo $key ?>" id="<?php echo $key ?>"
					   value="<?php echo isset( $social_urls[ $key ] ) ? $social_urls[ $key ] : '' ?>"
					   class="regular-text"
					   placeholder="<?php echo __( 'Add your site URL here.', THEME_DOMAIN ); ?>"/>
				<br/>
			</td>
		</tr>
	<?php } ?>
</table>
