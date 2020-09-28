<div class="thrv_wrapper thrv_text_element thrv_meta_elements">
	<div class="tcb-plain-text">
		<?php echo __( 'Posted in', THEME_DOMAIN ); ?>&nbsp;
		<span class="thrive-inline-shortcode">
			<span class="thrive-shortcode-content" contenteditable="false"
				  data-shortcode-name="<?php echo __( 'Post Categories', THEME_DOMAIN ); ?>"
				  data-id="tcb_post_categories"
				  data-shortcode="tcb_post_categories"
				  data-option-inline="1"
				  data-attr-url="0">
				<?php echo __( 'Uncategorized', THEME_DOMAIN ); ?>
			</span>
		</span>&nbsp;
		<?php echo __( 'on', THEME_DOMAIN ); ?>&nbsp;
		<span class="thrive-inline-shortcode">
			<span class="thrive-shortcode-content" contenteditable="false"
				  data-shortcode-name="<?php echo __( 'Published Date', THEME_DOMAIN ); ?>"
				  data-attr-format="<?php echo get_option( 'date_format' ); ?>"
				  data-id="tcb_post_published_date"
				  data-shortcode="tcb_post_published_date"
				  data-option-inline="1"
				  data-attr-url="0">
				<?php echo date_i18n( get_option( 'date_format' ) ); ?>
			</span>
		</span> <?php echo __( 'by', THEME_DOMAIN ); ?>&nbsp;
		<span class="thrive-inline-shortcode">
			<span class="thrive-shortcode-content" contenteditable="false"
				  data-shortcode-name="<?php echo __( 'Author name', THEME_DOMAIN ); ?>"
				  data-id="tcb_post_author_name"
				  data-shortcode="tcb_post_author_name"
				  data-option-inline="1"
				  data-attr-url="0">
				<?php echo __( 'Author name', THEME_DOMAIN ); ?>
			</span>
		</span>,&nbsp;
		<span class="thrive-inline-shortcode">
			<span class="thrive-shortcode-content" contenteditable="false"
				  data-shortcode-name="<?php echo __( 'Comments Number', THEME_DOMAIN ); ?>"
				  data-id="tcb_post_comments_number"
				  data-shortcode="tcb_post_comments_number"
				  data-option-inline="1"
				  data-attr-url="0">
				<?php echo __( 'No Comments', THEME_DOMAIN ); ?>
			</span>
		</span>
	</div>
</div>
