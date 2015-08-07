<?php
/**
 * class-gr-widget-webform.php
 *
 * @author Grzeogrz Struczynski <grzegorz.struczynski@implix.com>
 * http://getresponse.com
 */
class GR_Widget extends WP_Widget {

	var $GrOptionDbPrefix = 'GrIntegrationOptions_';

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'getresponse-widget',
			__( 'GetResponse Web Form', 'Gr_Integration' ),
			array( 'description' => __( 'Dispaly a GetResponse Web Form on your site.', 'Gr_Integration' ), )
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args	 Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		$webform_id = $instance['select'];
		$variants_id = $instance['variants'];
		$style_id = $instance['style'];
		$center = $instance['center'];
		$center_margin = $instance['center_margin'];
		$version = $instance['version'];

		$api_key = get_option($this->GrOptionDbPrefix . 'api_key');
		if ( !empty($api_key)) {
			$api = new GetResponseIntegration($api_key);
			$webform = ($version == 'old') ? $api->getWebForm($webform_id) : $api->getForm($webform_id);
		}

		// css styles Webform/Wordpress
		$css = ($style_id == 1 && $version == 'old') ? '&css=1' : null;
		$variant = ( $variants_id >= 0 && $version == 'new') ? '&v=' . (int)$variants_id : null;

		if ( !empty($webform) && isset($webform->scriptUrl) && in_array($webform->status, array('enabled', 'published')))
		{
			$div_start = $div_end = '';
			if ($center == '1') {
				$div_start = '<div style="margin-left: auto; margin-right: auto; width: ' . $center_margin . 'px;">';
				$div_end = '</div>';
			}

			$form = '<p>';
			$form .= $div_start . '<script type="text/javascript" src="' . htmlspecialchars($webform->scriptUrl . $css . $variant) .'"></script>' . $div_end;
			$form .= '</p>';
		}

		if (!empty($form))
		{
			echo $args['before_widget'];
			echo __( $form, 'text_domain' );
			echo $args['after_widget'];
		}
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 * @return string|void
	 */
	public function form( $instance ) {

		$select = ($instance) ? esc_attr($instance['select']) : '';
		$variants = ($instance) ? esc_attr($instance['variants']) : '';
		$style = ($instance) ? esc_attr($instance['style']) : '';
		$center = ($instance) ? esc_attr($instance['center']) : '';
		$center_margin = ($instance) ? esc_attr($instance['center_margin']) : '';

		$api_key = get_option($this->GrOptionDbPrefix . 'api_key');
		if ( !empty($api_key)) {
		?>
		<p>
			<div class="gr-loading-select"><img src="images/loading.gif"/></div>

			<div class="gr_webform_select" style="display: none;">
				<label for="<?php echo $this->get_field_id( 'select' ); ?>"><?php _e( 'Web Form:' ); ?></label>
				<select name="<?php echo $this->get_field_name('select'); ?>" id="<?php echo $this->get_field_id( 'select' ); ?>" class="widefa" onchange="setVariants(jQuery(this));">
					<optgroup label="<?php _e( 'New Forms' ); ?>" id="gr-optgroup-new"></optgroup>
					<optgroup label="<?php _e( 'Old Web Forms' ); ?>" id="gr-optgroup-old"></optgroup>
				</select>
			</div>

			<div class="gr-loading"><img src="images/loading.gif"/></div>

			<div class="grvariants" style="display: none;">
				<label style="padding-right: 19px;" for="<?php echo $this->get_field_id( 'variants' ); ?>"><?php _e( 'Variant:' ); ?></label>
				<select name="<?php echo $this->get_field_name('variants'); ?>" id="<?php echo $this->get_field_id( 'variants' ); ?>" class="widefa grvariants_select">

				</select>
			</div>

		</p>
		<p id="gr_css_style">
			<input id="<?php echo $this->get_field_id('style'); ?>" name="<?php echo $this->get_field_name('style'); ?>" type="checkbox" value="1" <?php checked( '1', $style ); ?> />
			<label for="<?php echo $this->get_field_id('style'); ?>"><?php _e('Use Wordpress CSS styles (Old Web Forms)', 'Gr_Integration'); ?></label>
		</p>
		<p id="gr_center">
			<input id="<?php echo $this->get_field_id('center'); ?>" name="<?php echo $this->get_field_name('center'); ?>" type="checkbox" value="1" <?php checked( '1', $center ); ?> />
			<label for="<?php echo $this->get_field_id('center'); ?>"><?php _e('Center Webform', 'Gr_Integration'); ?></label>

			<label for="<?php echo $this->get_field_id('center_margin'); ?>"> (<?php _e('Margin:', 'Gr_Integration'); ?></label>
			<input id="<?php echo $this->get_field_id('center_margin'); ?>" name="<?php echo $this->get_field_name('center_margin'); ?>" type="text" value="<?php echo !empty($center_margin) ? $center_margin : '200'; ?>" size="4"/>px)
		</p>
		<p id="gr_version">
			<input id="<?php echo $this->get_field_id('version'); ?>" name="<?php echo $this->get_field_name('version'); ?>" type="hidden" value="old" size="4"/>
		</p>

		<script type="text/javascript">
			jQuery(document).ready(function($){
				var select_name = '<?php echo $this->get_field_id( 'select' ); ?>';

				getOldWebForms($('#'+select_name));

				function getNewWebforms(selector) {
					var parent = selector.parent().parent();
					var variants_loader = parent.find('.gr-loading-select');
					var gr_webform_select = parent.find('.gr_webform_select');
					var variants_options = parent.find('.grvariants');
					var select = '<?php echo $select; ?>';
					var variant = '<?php echo $variants; ?>';

					$.ajax({
						url: 'admin-ajax.php',
						data: {
							'action': 'gr-forms-submit'
						},
						beforeSend: function() {
							variants_options.hide();
							variants_loader.show();
						},
						success: function (response) {
							if (response.success && response.success.httpStatus !== 404)
							{
								var html = '';
								$.each(response.success, function(key, obj) {
									if (obj.status == 'published') {
										var selected = (obj.formId == select) ? 'selected="selected"' : '';
										var has_variatns = (obj.hasVariants && obj.hasVariants == 'true') ? 1 : 0;
										var campaign_name = (obj.campaign.name != undefined) ? ' (' + obj.campaign.name + ')' : '';
										html += '<option data-version="new" data-variants="'+has_variatns+'" id="'+obj.formId+'" value="' + obj.formId + '" '+selected+'>' + obj.name + campaign_name + '</option>';
									}
								});

								html = (html != '') ? html : '<option value="-" disabled>No webforms</option>';
								selector.find('#gr-optgroup-new').html(html);
							}
							else {
								variants_selector.html('<option value="-">-</option>');
							}
						},
						complete: function () {
							gr_webform_select.show();
							variants_loader.hide();

							if (variant >= 0) {
								setVariants(selector);
							}
						}
					});
				}

				function getOldWebForms(selector) {
					var parent = selector.parent().parent();
					var variants_loader = parent.find('.gr-loading-select');
					var variants_options = parent.find('.grvariants');
					var select = '<?php echo $select; ?>';

					$.ajax({
						url: 'admin-ajax.php',
						data: {
							'action': 'gr-webforms-submit'
						},
						beforeSend: function() {
							variants_options.hide();
							variants_loader.show();
						},
						success: function (response) {
							if (response.success && response.success.httpStatus !== 404)
							{
								var html = '';
								$.each(response.success, function(key, obj) {
									if (obj.status == 'enabled') {
										var selected = (obj.webformId == select) ? 'selected="selected"' : '';
										var campaign_name = (obj.campaign.name != undefined) ? ' (' + obj.campaign.name + ')' : '';
										html += '<option data-version="old" id="'+obj.webformId+'" value="' + obj.webformId + '" '+selected+'>' + obj.name + campaign_name + '</option>';
									}
								});

								html = (html != '') ? html : '<option value="-" disabled>No webforms</option>';
								selector.find('#gr-optgroup-old').html(html);
							}
							else {
								variants_selector.html('<option value="-">-</option>');
							}
						},
						complete: function () {
							variants_loader.hide();
							getNewWebforms($('#'+select_name));
						}
					});
				}

				function setVariants(selector) {
					var form_id = selector.find(':selected').attr('id');
					var has_variant = selector.find(':selected').attr('data-variants');
					var version = selector.find(':selected').attr('data-version');
					var parent = selector.parent().parent();
					var variants_selector = parent.find('.grvariants_select');
					var variants_loader = parent.find('.gr-loading');
					var variants_options = parent.find('.grvariants');
					var version_options = parent.find('#gr_version input');
					version_options.val(version);

					if (has_variant == '1') {
						var selected_variant = '<?php echo $variants; ?>';
						var select = '<?php echo $select; ?>';
						$.ajax({
							url: 'admin-ajax.php',
							data: {
								'action': 'gr-variants-submit',
								'form_id': form_id
							},
							beforeSend: function() {
								variants_options.hide();
								variants_loader.show();
							},
							success: function (response) {
								if (response.success && response.success.httpStatus !== 404)
								{
									var html = '';
									$.each(response.success, function(key, obj) {
										if (obj.status == 'enabled') {
											var selected = (obj.variant == selected_variant && obj.formId == select) ? 'selected="selected"' : '';
											html += '<option value="' + obj.variant + '" '+selected+'>' + obj.variantName + '</option>';
										}
									});

									variants_selector.html(html);
									variants_options.show();
								}
								else {
									variants_selector.html('<option value="-">-</option>');
								}
							},
							complete: function () {
								variants_loader.hide();
							}
						});
					}
					else {
						variants_selector.html('<option value="-">-</option>');
						variants_options.hide();
					}
				}
			});

			function setVariants(selector) {
					var form_id = selector.find(':selected').attr('id');
					var has_variant = selector.find(':selected').attr('data-variants');
					var version = selector.find(':selected').attr('data-version');
					var parent = selector.parent().parent();
					var variants_selector = parent.find('.grvariants_select');
					var variants_loader = parent.find('.gr-loading');
					var variants_options = parent.find('.grvariants');
					var version_options = parent.find('#gr_version input');
					version_options.val(version);

					if (has_variant == '1') {
						var selected_variant = '<?php echo $variants; ?>';
						var select = '<?php echo $select; ?>';
						jQuery.ajax({
							url: 'admin-ajax.php',
							data: {
								'action': 'gr-variants-submit',
								'form_id': form_id
							},
							beforeSend: function() {
								variants_options.hide();
								variants_loader.show();
							},
							success: function (response) {
								if (response.success && response.success.httpStatus !== 404)
								{
									var html = '';
									jQuery.each(response.success, function(key, obj) {
										if (obj.status == 'enabled') {
											var selected = (obj.variant == selected_variant && obj.formId == select) ? 'selected="selected"' : '';
											html += '<option value="' + obj.variant + '" '+selected+'>' + obj.variantName + '</option>';
										}
									});

									variants_selector.html(html);
									variants_options.show();
								}
								else {
									variants_selector.html('<option value="-">-</option>');
									variants_options.hide();
								}
							},
							complete: function () {
								variants_loader.hide();
							}
						});
					}
					else {
						variants_selector.html('<option value="-">-</option>');
						variants_options.hide();
					}
				}
		</script>

		<?php
		}
		else {
			?>
		<p><?php _e('API key is not set.', 'Gr_Integration'); ?></p>
		<?php
		}
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['select'] = !empty($new_instance['select']) ? strip_tags($new_instance['select']) : null;
		$instance['variants'] = $new_instance['variants'] !== '-' ? strip_tags($new_instance['variants']) : null;
		$instance['style'] = strip_tags($new_instance['style']);
		$instance['center'] = strip_tags($new_instance['center']);
		$instance['center_margin'] = (int)strip_tags($new_instance['center_margin']);
		$instance['version'] = (in_array(strip_tags($new_instance['version']), array('old','new'))) ? strip_tags($new_instance['version']) : 'old';

		return $instance;
	}

} // class GR_Widget