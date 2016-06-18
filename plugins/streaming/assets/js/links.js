(function($) {
	'use strict'

	app.viewModels.links = {

		/**
		 * Holds all links.
		 * 
		 * @type ko.observable(Array)
		 */
		sourceItems: ko.observableArray(),

		/**
		 * Delete links that have more reports then this.
		 * 
		 * @type int
		 */
		numOfReports: ko.observable(),

		approveLink: function(link) {
			var self = this;

			app.utils.ajax({
				url: vars.urls.baseUrl + '/links/' + link.id + '/approve',
				data: ko.toJSON({ _token: vars.token}),
				success: function(data) {
					app.utils.noty(data, 'success');

					$('[data-id='+link.id+']').html('Yes');
				}
			});
		},

		deleteLinks: function() {
			var self = this;

			app.utils.ajax({
				url: vars.urls.baseUrl + '/links/delete',
				data: ko.toJSON({ _token: vars.token, number: self.numOfReports }),
				success: function(data) {
					location.reload(false);
				}
			});
		},

		/**
		 * Delete item from context source items and database.
		 * 
		 * @param  Object item
		 * @return void
		 */
		deleteItem: function(item) {

			app.utils.ajax({
				url: vars.urls.baseUrl + '/links/' + item.id + '/delete',
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
		uri: 'links',
	};

	app.viewModels.titles.index.params.availToStream = ko.observable();

})(jQuery);