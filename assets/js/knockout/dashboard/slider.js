(function($) {
	'use strict'

	app.viewModels.slider = {

		/**
		 * Holds all the existing slides.
		 * 
		 * @type ko.observable(Array)
		 */
		allSlides: ko.observableArray(),

		saveSlide: function(form) {
			var self   = this,
				exists = false;

			app.models.slide._token = vars.token;
				
			app.utils.ajax({
				url: form.action,
				data: ko.toJSON(app.models.slide),
				error: function(data) {
					app.utils.enablePage();
					app.utils.noty(data.responseJSON, 'error');
				}
			});
		
			//check if title exists already
			$.each(self.allSlides(), function(i,v) {
				if (v.title.toLowerCase() == app.models.slide.title().toLowerCase()) {
					exists = true;
					return false;
				}
			});

			//only push the slide to all slides array if titles doesnt exist already
			if ( ! exists) {
				self.allSlides.push(ko.toJS(app.models.slide));
			}

			//empty form fields
			$.each(app.models.slide, function(i, observable) {
				if (ko.isObservable(observable)) {
					observable(null);
				};
			});

			$('#add-new').collapse('hide');
		},

		/**
		 * Show/hide create new slide panel.
		 * 
		 * @return void
		 */
		collapse: function() {
			$('#add-new').collapse('toggle');
		},

		/**
		 * Send request to server to delete given slide
		 * and remove it from allSlides array.
		 * 
		 * @param  {Object} slide
		 * @return void
		 */
		removeSlide: function(slide) {
			if (slide.id) {
				app.utils.ajax({
					url: $('#save-slide')[0].action.replace('add', 'remove'),
					data: ko.toJSON({ _token: vars.token, id: slide.id }),
					success: function(data) {
						app.utils.noty(data, 'success');
						app.viewModels.slider.allSlides.remove(slide);
					},
					error: function(data) {
						app.utils.enablePage();
						app.utils.noty(data.responseJSON, 'error');
					}
				});
			}
		},

		/**
		 * Populate new slide panel values with the
		 * ones from slide we want to edit and toggle the panel.
		 * 
		 * @param  Object slide
		 * @return void
		 */
		edit: function(slide) {
			
			app.models.slide.title(slide.title);
			app.models.slide.body(slide.body);
			app.models.slide.director(slide.director);
			app.models.slide.genre(slide.genre);
			app.models.slide.stars(slide.stars);
			app.models.slide.link(slide.link);
			app.models.slide.image(slide.image);
			app.models.slide.trailer(slide.trailer);
			app.models.slide.poster(slide.poster);
			app.models.slide.trailer_image(slide.trailer_image);
			app.models.slide.id(slide.id);

			$('#add-new').collapse('show');

			window.scrollTo(0,0);
		},

		/**
		 * Holds query with which to fetch matching records
		 * for autopopulating slide data.
		 * 
		 * @type ko.observable(String),
		 */
		populateQuery: ko.observable(),

		/**
		 * Holds the auto populate query results,
		 * 
		 * @type ko.observable(Array),
		 */
		populateResults: ko.observableArray(),

		/**
		 * Populate new slide with the result of 
		 * autocomplete query data.
		 * 
		 * @param  {Object} result
		 * @return void
		 */
		populateSlide: function(result) {

			//populate new slide model with values from
			//autocomplete query results
			app.models.slide.title(result.title);
			app.models.slide.body(result.plot);
			app.models.slide.genre(result.genre);
			app.models.slide.image(result.background);
			app.models.slide.trailer(result.trailer);
			app.models.slide.poster(result.poster);

			//construct the url
			var url = vars.urls.baseUrl+'/'+(result.type == 'movie' ? 'movies' : 'series')+'/'+result.id+'-'+result.title.replace(/\s+/g, '-').toLowerCase();
			app.models.slide.link(url);

			if (result.director[0]) {
				app.models.slide.director(result.director[0].name);
			}

			if (result.image[0]) {
				app.models.slide.trailer_image(result.image[0].path);
			}

			//concatanate names of 3 stars into a string and then
			//push that to model
			var stars = '';

			$.each(result.actor, function(i,v) {
				if (i < 3) {
					stars = stars + (v.name + ', ');
				} else {
					return false;
				}
			});
			
			//remove traling comma
			app.models.slide.stars(stars.replace(/,\s*$/, ""));

			$('.autocomplete-container-modal').hide();
		},
	};

	/**
	 * Fetches data for populating slide using the query.
	 * 
	 * @return void
	 */
	app.viewModels.slider.fetchAutoPopulateData = ko.computed(function() {
	    var self  = this,
	    	query = self.populateQuery();

	        //bail if query is falsy
	        if ( ! query) return;

	        app.utils.ajax({
	            url: vars.urls.baseUrl + '/populate-slider/' + query,
	            data: ko.toJSON({ _token: vars.token }),
	            success: function(data) {
	            	self.populateResults.removeAll();
	                self.populateResults(data);
	            }
	        });

	}, app.viewModels.slider).extend({ rateLimit: { method: "notifyWhenChangesStop", timeout: 200 } });

	app.models.slide =  {

		/**
		 * New slide title.
		 * 
		 * @type ko.observable(String),
		 */
		title: ko.observable(),

		/**
		 * New slide body.
		 * 
		 * @type ko.observable(String),
		 */
		body: ko.observable(),

		/**
		 * New slide link.
		 * 
		 * @type ko.observable(String),
		 */
		link: ko.observable(),

		genre: ko.observable(),
		director: ko.observable(),
		stars: ko.observable(),
		trailer: ko.observable(),
		trailer_image: ko.observable(),
		poster: ko.observable(),

		/**
		 * New slide image link.
		 * 
		 * @type ko.observable(String),
		 */
		image: ko.observable(),

		/**
		 * Slide id.
		 * 
		 * @type ko.observable(String),
		 */
		id: ko.observable(),
	};

})(jQuery);