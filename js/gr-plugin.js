(function() {
	tinymce.PluginManager.add('GrShortcodes', function(editor, url) {

		function getValues() {
			var wf = [];

			wf.push({text:text_new_webfoms, url:null, disabled:true});

			setOption(my_forms, wf);

			wf.push({text:text_old_webfoms, url:null, disabled:true});

			setOption(my_webforms, wf);

			return wf;
		}

		editor.addButton('GrShortcodes', {
			type: 'listbox',
			title: 'GetResponse Web form integration',
			text: 'GR Web form',
			values: getValues(),
			onselect: function(v) {
				if (v.control.settings.url != null && v.control.settings.text != 'No web forms') {
					var shortcode = '[grwebform url="' + v.control.settings.url + '" css="on" center="off" center_margin="200"/]';
					editor.insertContent(shortcode);
				}
			}
		});
	});

	function setOption(items, wf)
	{
		if (typeof(items) !== 'undefined' && items != null) {

			for (var i in items) {

				var webforms = {};
				var campaign_id = (typeof my_campaigns === 'object' && typeof items[i].campaign !== 'undefined') ? items[i].campaign.campaignId : null;
				var webform_name = (campaign_id != null) ? items[i].name + ' (' + my_campaigns[campaign_id].name + ')' : items[i].name;

				webforms.text = webform_name;
				webforms.url = items[i].scriptUrl;

				wf.push(webforms);
			}
		}else {
			wf.push({text:text_no_webfoms, url:null});
		}
	}

})();