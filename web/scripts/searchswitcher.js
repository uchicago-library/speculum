/*
 * Switch the basic and advanced searches for the speculum project.
 */

var Searchswitcher = Class.create({
	initialize: function() {
		document.observe('dom:loaded', function() {
			$('advancedsearch').hide();
			$('basicsearchlink').observe('click', this.searchswitcher);
			$('advancedsearchlink').observe('click', this.searchswitcher);
		}.bind(this));
	},
	searchswitcher: function(e) {
		if ($('basicsearch').visible()) {
			$('basicsearch').hide();
			$('advancedsearch').show();
		} else {
			$('basicsearch').show();
			$('advancedsearch').hide();
		}
		e.stop();
	}
});


