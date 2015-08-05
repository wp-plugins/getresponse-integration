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
		$variant = ( !empty($variants_id) && $version == 'new') ? '&v=' . (int)$variants_id : null;

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
		$saved_version = ($instance) ? esc_attr($instance['version']) : '';

		$version = 'old';
		$api_key = get_option($this->GrOptionDbPrefix . 'api_key');
		if ( !empty($api_key)) {
			$campaigns = array();
			$api = new GetResponseIntegration($api_key);
			$results = $api->getCampaigns();
			if ( !empty($results) && $api->http_status == '200' ) {
				foreach ($results as $result) {
					if (isset($result->campaignId) && isset($result->name)) {
						$campaigns[$result->campaignId] = $result->name;
					}
				}
				$webforms = $api->getWebforms(array('sort' => array('name' => 'asc')));
				$forms	= $api->getForms(array('sort' => array('name' => 'asc')));
			}

			if ( !empty($saved_version) && $saved_version == 'new')
			{
				$current_variants = $api->getFormVariants($select);
			}

			$is_variant = ( !empty($current_variants) && $api->http_status == 200 && $variants >= 0 ) ? true : false;
			$display = ($is_variant === true) ? '' : 'style="display: none;"';
			$hide_css = true;
		?>
		<p>
			<?php
			if ( !empty($webforms) || !empty($forms)) { ?>
			<label for="<?php echo $this->get_field_id( 'select' ); ?>"><?php _e( 'Web Form:' ); ?></label>
			<select name="<?php echo $this->get_field_name('select'); ?>" id="<?php echo $this->get_field_id( 'select' ); ?>" class="widefa">
				<optgroup label="<?php _e( 'New Web forms' ); ?>">
				<?php
					$total = 0;
					if ( !empty($forms)) {
						foreach ($forms as $form) {
							if (isset($form->hasVariants) && isset($form->webformId) && isset($form->name) && isset($form->campaign->campaignId) && isset($form->status) && $form->status == 'published') {
								$v = ($form->hasVariants === 'true') ? 1 : 0;
								echo '<option data-version="new" data-variants="' . $v . '" value="' . $form->webformId . '" id="' . $form->webformId . '"', $select == $form->webformId ? ' selected="selected"' : '', '>', $form->name . ' (' . $campaigns[$form->campaign->campaignId] . ')', '</option>';
								$total++;

								if ($select == $form->webformId) {
									$version = 'new';
								}
							}
						}
					}
					if ($total == 0) {
						echo "<option disabled>" . __( 'No Web Forms' ) . " </option>";
					}
				?>
				</optgroup>
				<optgroup label="<?php _e( 'Old Web forms' ); ?>">
				<?php
					$total = 0;
					if ( !empty($webforms) ) {
						foreach ($webforms as $webform) {
							if (isset($webform->webformId) && isset($webform->name) && isset($webform->campaign->campaignId) && isset($webform->status) && $webform->status == 'enabled') {
								echo '<option data-version="old" value="' . $webform->webformId . '" id="' . $webform->webformId . '"', $select == $webform->webformId ? ' selected="selected"' : '', '>', $webform->name . ' (' . $campaigns[$webform->campaign->campaignId] . ')', '</option>';
								$total++;
								//old webform, do not display "Use Wordpress CSS styles" option
								if ($select == $webform->webformId) {
									$hide_css = false;
								}
							}
						}
					}
					if ($total == 0) {
						echo "<option disabled>" . __( 'No Web Forms' ) . " </option>";
					}
				?>
				</optgroup>
			</select>

			<div class="gr-loading"><img src="images/loading.gif"/></div>

			<div class="grvariants" <?php echo $display; ?>>
				<label for="<?php echo $this->get_field_id( 'variants' ); ?>"><?php _e( 'Variant:' ); ?></label>
				<select name="<?php echo $this->get_field_name('variants'); ?>" id="<?php echo $this->get_field_id( 'variants' ); ?>" class="widefa grvariants_select">
					<?php
						if ( $is_variant === true && !empty($current_variants)) {
							$added = array();
							foreach ($current_variants as $current_variant) {
								if ($current_variant->status == 'enabled' && !in_array($current_variant->variant, $added)) {
									echo '<option value="' . $current_variant->variant . '"', $variants == $current_variant->variant ? ' selected="selected"' : '', '>', $current_variant->variantName, '</option>';
								}
								array_push($added, $current_variant->variant);
							}
						}
					?>
				</select>
			</div>

			<?php } else {
				_e('No Webforms', 'Gr_Integration');
				}
			?>
		</p>
		<p id="gr_css_style" <?php echo ($hide_css === true)  ? 'style="display: none;"' : ''; ?>>
			<input id="<?php echo $this->get_field_id('style'); ?>" name="<?php echo $this->get_field_name('style'); ?>" type="checkbox" value="1" <?php checked( '1', $style ); ?> />
			<label for="<?php echo $this->get_field_id('style'); ?>"><?php _e('Use Wordpress CSS styles', 'Gr_Integration'); ?></label>
		</p>
		<p id="gr_center">
			<input id="<?php echo $this->get_field_id('center'); ?>" name="<?php echo $this->get_field_name('center'); ?>" type="checkbox" value="1" <?php checked( '1', $center ); ?> />
			<label for="<?php echo $this->get_field_id('center'); ?>"><?php _e('Center Webform', 'Gr_Integration'); ?></label>

			<label for="<?php echo $this->get_field_id('center_margin'); ?>"> (<?php _e('Margin:', 'Gr_Integration'); ?></label>
			<input id="<?php echo $this->get_field_id('center_margin'); ?>" name="<?php echo $this->get_field_name('center_margin'); ?>" type="text" value="<?php echo !empty($center_margin) ? $center_margin : '200'; ?>" size="4"/>px)
		</p>
		<p id="gr_version">
			<input id="<?php echo $this->get_field_id('version'); ?>" name="<?php echo $this->get_field_name('version'); ?>" type="hidden" value="<?php echo !empty($version) ? $version : 'old'; ?>" size="4"/>
		</p>

		<script type="text/javascript">
			jQuery(document).ready(function($){
				var select_name = '<?php echo $this->get_field_id( 'select' ); ?>';

				$('#'+select_name).change(function () {
					var selector = $(this);
					var form_id = selector.find(':selected').attr('id');
					var has_variant = selector.find(':selected').attr('data-variants');
					var version = selector.find(':selected').attr('data-version');
					var parent = selector.parent().parent();
					var variants_selector = parent.find('.grvariants_select');
					var variants_loader = parent.find('.gr-loading');
					var variants_options = parent.find('.grvariants');
					var css_options = parent.find('#gr_css_style');
					var version_options = parent.find('#gr_version input');
					version_options.val(version);

					if (has_variant == '1') {
						var selected_variant = '<?php echo $variants; ?>';
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
								if (response.success)
								{
									var html = '';
									var added = [];
									$.each(response.success, function(key, obj) {
										if (obj.status == 'enabled' && jQuery.inArray( obj.variant, added ) == -1) {
											var selected = (obj.variant == selected_variant) ? 'selected="selected"' : '';
											html += '<option value="' + obj.variant + '" '+selected+'>' + obj.variantName + '</option>';
										}
										added.push(obj.variant);
									});

									variants_selector.html(html);
									variants_loader.hide();
									variants_options.show();
								}
							}
						});
					}
					else {
						variants_selector.html('<option value="-">-</option>');
						variants_options.hide();
					}

					if (typeof has_variant === 'undefined') {
						css_options.show();
					}
					else {
						css_options.hide();
					}
				});


			});
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