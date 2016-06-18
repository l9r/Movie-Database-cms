(function($) {
	'use strict'

	app.viewModels.titles.index = {

		/**
		 * Stores all the games in their original state.
		 * 
		 * @type Array
		 */
		sourceItems: ko.observableArray(),

		/**
		 * Latest genre user has selected.
		 * 
		 * @type string,
		 */
		genre: ko.observableArray(),

		lazyLoadImage: function(item) {
			$(item).find('img').lazyload();
		},

		/**
		 * Stores all parameters user is currently restricting titles query by.
		 * 
		 * @type {Object}
		 */
		params: {

			/**
			 * Currently selected sorting option.
			 * 
			 * @type ko.observable(String)
			 */
			order: ko.observable('mc_num_of_votesDesc'),

			/**
			 * Stores users query to filters games on.
			 * 
			 * @type ko.observable(String),
			 */
			query: ko.observable().extend({ rateLimit: { method: "notifyWhenChangesStop", timeout: 400 } }),

			/**
			 * Filter titles that were released after this date.
			 * 
			 * @type ko.observable(String),
			 */
			after: ko.observable(),

			/**
			 * Filter titles that were released before this date.
			 * 
			 * @type ko.observable(String),
			 */
			before: ko.observable(),

			/**
			 * Filter movies to only ones that specified actors appeared in.
			 * 
			 * @type string
			 */
			cast: ko.observable(),

			/**
			 * Filter titles by genres.
			 * 
			 * @type string,
			 */
			genres: ko.observableArray(),

			type: ko.observable(),

			minRating: ko.observable(),

			maxRating: ko.observable(),
		},

		/**
		 * Uri to hit for paginated results.
		 * 
		 * @type {String}
		 */
		uri: 'titles',

		/**
		 * Remove a genre from genres array.
		 * 
		 * @param  string genre
		 * @return void
		 */
		removeGenre: function(genre) {
			app.viewModels.titles.index.params.genres.remove(genre);
		},

		start: function(type) {
			var self   = this,
				genres = app.utils.getUrlParams('genre'),
				page   = window.location.hash,
				paginations = $('.index-pagination');

			self.params.type(type);

			//filter by genres if they're present in url params
			if (genres) {
				$.each(genres.toLowerCase().split(','), function(i,v) {
					$('[value="'+ v.toLowerCase()+'"]').iCheck('check');
							
					if (self.params.genres.indexOf(v) === -1) {
						self.params.genres.push(v);
					}
				});
			}

			//push a genre user selects to genres array
			self.genre.subscribe(function(value) {
				if (value) {
					self.params.genres.push(value);
				}
			});

			//set a page if we found any in url hash
			if (page) {
				app.paginator.currentPage(page.replace('#page-', ''));
			}

			//change current page on url hash change
			$(window).bind('hashchange', function() {
				var hash = location.hash.slice(1);

				if (hash.indexOf('page-') > -1) {
					app.paginator.currentPage(hash.replace('page-', ''));
					paginations.pagination('selectPage', hash.replace('page-', '')).pagination('redraw');
				}		
			});

			app.paginator.start(app.viewModels.titles.index, '#content', 18).success(function(data) {
				paginations.pagination({
	                items: app.paginator.totalItems(),
	                itemsOnPage: app.paginator.perPage(),
	                displayedPages: 8,
	                selectOnClick: false,
	                prevText: vars.trans.prev,
	                nextText: vars.trans.next,
	                //currentPage: page ? page.replace('#page-', '')) : 1,
	            });

				if (page) {
					paginations.pagination('selectPage', page.replace('#page-', '')).pagination('redraw');
				}
	            
			});

			app.paginator.paginate.subscribe(function(promise) {
				promise.success(function(data) {
					paginations.pagination('updateItems', app.paginator.totalItems());
					paginations.pagination('updateItemsOnPage', app.paginator.perPage());
				});
			   
			});

			//scroll top when botom pagination is used
			$('.bottom-pagination').on('click', function(e) {
				if ( ! $(e.target).parent().is('.active')) {
					var body = $('html, body');
					body.animate({scrollTop:0});
				}
			});
		},
	};

})(jQuery);