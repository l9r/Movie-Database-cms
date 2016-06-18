(function($) {
	'use strict';

	app.viewModels.home = {};

	/**
	 * Inniate slider.
	 *
	 * @type Object
	 */
	ko.bindingHandlers.slider = {

		init: function() {
			$(document).ready(function(){
	          $('.home-slider').slick({
	            dots: true,
	            autoPlay: true,
	            fade: true
	          });
	        });
		}
	};


})(jQuery);