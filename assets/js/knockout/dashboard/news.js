(function($) {
	'use strict'

	app.viewModels.news = {

		/**
		 * Holds all titles.
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

			app.utils.ajax({
				url: vars.urls.baseUrl + '/' + vars.trans.news + '/' + item.id,
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
			 * Currently selected sorting option.
			 * 
			 * @type ko.observable(String)
			 */
			order: ko.observable(),

			/**
			 * Currently selected type.
			 * 
			 * @type ko.observable(String)
			 */
			type: ko.observable(),

			/**
			 * Stores users query to filters games on.
			 * 
			 * @type ko.observable(String),
			 */
			query: ko.observable().extend({ rateLimit: { method: "notifyWhenChangesStop", timeout: 400 } }),
		},

		/**
		 * Uri to hit for paginated results.
		 * 
		 * @type {String}
		 */
		uri: 'news',
	};

})(jQuery);