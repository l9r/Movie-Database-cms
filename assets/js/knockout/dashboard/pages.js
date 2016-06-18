(function($) {
	'use strict'

	app.viewModels.pages = {

		/**
		 * Holds all pages.
		 * 
		 * @type ko.observable(Array)
		 */
		sourceItems: ko.observableArray(),

		deleteItem: function(item) {
			app.utils.ajax({
				url: vars.urls.baseUrl + '/pages/' + item.id,
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

			visibility: ko.observable(),
		},

		/**
		 * Uri to hit for paginated results.
		 * 
		 * @type {String}
		 */
		uri: 'pages',
	};

})(jQuery);