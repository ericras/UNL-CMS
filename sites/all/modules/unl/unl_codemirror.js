(function($) {

  Drupal.behaviors.unl_codemirror = {
	attach: function(context, settings) {

		/**
		* If the user selected a text format (html) configured to be used with the editor,
		* show it, else show the default textarea.
		*/
		function acifyWrapper($textFormatWrapper) {

			// The select list for chosing the text format that will be used.
			var $filterSelector = $textFormatWrapper.find('select.filter-list');

			// Checks if the currently selected filter is html
			if ($filterSelector.val() == 'html') {





				// Iterate through all textarea containers that and attach the editor.
				$('div.form-item.form-type-textarea:visible', $textFormatWrapper).each(function(i) {


					// Initialize the editor and set the correct options.
					var editor_instance = CodeMirror.fromTextArea($(this).find('textarea').get(0), {
						lineNumbers: true,
			            mode: 'htmlmixed',
			            tabMode: 'shift'
			        });
					$(this).data('editor_instance', editor_instance);


				});

			} else { // Show the textarea.

				$('div.form-item.form-type-textarea:visible', $textFormatWrapper).each(function(i) {
					if ($(this).data('editor_instance')) {
						$(this).data('editor_instance').toTextArea();
					}
				});

			}
		}

		/**
		 * Bind the change event to all text format select lists.
		 */
		$('div.text-format-wrapper fieldset.filter-wrapper select.filter-list')
			// Executes before the Drupal.behaviors.attachWysiwyg .change()
			.change(function(e) {
				var $textFormatWrapper = $(this).parents('div.text-format-wrapper:first');
				acifyWrapper($textFormatWrapper);
			})
			// Executes after the Drupal.behaviors.attachWysiwyg .change()
			.live('change', function(e) {
				var $textFormatWrapper = $(this).parents('div.text-format-wrapper:first');
				var $filterSelector = $textFormatWrapper.find('select.filter-list');

				if ($filterSelector.val() == 'html') {
					$('textarea', $textFormatWrapper).hide();

					$('.grippie', $textFormatWrapper).hide();
				}
			}
		);

	}
  };

})(jQuery);
