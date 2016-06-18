(function($) {
	'use strict'

	app.viewModels.dashReviews = {

		/**
		 * Holds all pages.
		 * 
		 * @type ko.observable(Array)
		 */
		sourceItems: ko.observableArray(),

		/**
		 * Delete item from context source items and database.
		 * 
		 * @param  Object item
		 * @return void
		 */
		deleteItem: function(item) {
			console.log('a');
			app.utils.ajax({
				url: vars.movies + '/reviews/' + item.id,
				type: 'DELETE',
				data: ko.toJSON({ _token: vars.token }),
				success: function(data) {
					app.paginator.context.sourceItems.remove(item);
					app.utils.noty(data, 'success');
				},
				error: function(data) {
					app.utils.enablePage();
					app.utils.noty(data.responseJSON, 'error');
				}
			});
		},

		/**
		 * Any params paginator should filter or sort results on.
		 * 
		 * @type {Object}
		 */
		params: {
			/**
			 * Query to filter results on.
			 * 
			 * @type ko.observable(String),
			 */
			query: ko.observable().extend({ rateLimit: { method: "notifyWhenChangesStop", timeout: 400 } }),

			type: ko.observable(),
		},

		/**
		 * Uri to hit for paginated results.
		 * 
		 * @type {String}
		 */
		uri: 'reviews',
	};

})(jQuery);