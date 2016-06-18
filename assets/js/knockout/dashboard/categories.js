(function($) {
	'use strict'

	app.viewModels.categories = {

		/**
         * Holds autocomplete query results.
         * 
         * @type ko.observable(Array)
         */
        autocompleteResults: ko.observableArray(),

        /**
         * autocomplete query.
         * 
         * @type string
         */
        query: ko.observable(),

        /**
         * Query type.
         * 
         * @type string
         */
        queryType: ko.observable('title'),

		/**
		 * Holds all titles.
		 * 
		 * @type ko.observable(Array)
		 */
		sourceItems: ko.observableArray(),

		/**
		 * Active category.
		 * 
		 * @type mixed
		 */
		active: ko.observable(),

		/**
		 * Set given category as active one.
		 * 
		 * @param objecy category
		 */
		setActiveCategory: function(category, e) {

			if (e) {
				var $li = $(e.currentTarget);
			} else {
				var $li = $('#categories-list > ul > li').first();
			}
			
			//handle active state
			$li.siblings().removeClass('active');
			$li.addClass('active');

			//wrap titles and actors in observable array if needed
			if (typeof(category.title) !== "function") {
				category.title = ko.observableArray(category.title);
			}
			if (typeof(category.actor) !== "function") {
				category.actor = ko.observableArray(category.actor);
			}

			app.viewModels.categories.active(category);

			//make sure css has taken affect before resizing images
			setTimeout(function() {
				app.viewModels.categories.resizeImages();
			}, 100);
		},

		/**
		 * Attach title or actor to category.
		 * 
		 * @param  object resource  
		 * @param  object category
		 * @return void        
		 */
		attach: function(resource) {
			var self = app.viewModels.categories,
				type = resource.name ? 'actor' : 'title';

			app.utils.ajax({
                data: ko.toJSON({
                	type: type,
                	_token: vars.token, 
                	titleId: resource.id,
                	categoryId: self.active().id
                }),
                url: vars.urls.baseUrl+'/'+'categories'+'/attach',
                success: function(resp) {
                	self.active()[type].unshift(resource);
                   	app.utils.enablePage();
                   	self.query('');

                   	$('#category-search .autocomplete-container').hide();
                },
                error: function(resp) {
                	app.utils.enablePage();
                	app.utils.noty(resp.responseJSON, 'error');
                }
            }); 
		},

		/**
		 * Detach title or actor from category.
		 * 
		 * @param  object resource   
		 * @param  object category
		 * @return void        
		 */
		detach: function(resource) {
			var self = app.viewModels.categories,
				type = resource.name ? 'actor' : 'title';

			app.utils.disablePage();

			app.utils.ajax({
                data: ko.toJSON({
                	type: type,
                	_token: vars.token, 
                	titleId: resource.id,
                	categoryId: self.active().id
                }),
                url: vars.urls.baseUrl+'/'+'categories'+'/detach',
                success: function(resp) {
      				self.active()[type].remove(resource);
                	app.utils.enablePage();
                },
                error: function(resp) {
                	app.utils.enablePage();
                	app.utils.noty(resp.responseJSON, 'error');
                }
            }); 
		},

		showEditModal: function(category) {
			$.each(category, function(i,v) {

				//to camel case
				var c = i.replace(/(\_[a-z])/g, function($1){return $1.toUpperCase().replace('_','');});

				if(typeof app.models.category[c] !== 'undefined') {

					app.models.category[c](v);
				}
			});

			app.models.category.id = ko.observable(category.id);

			$('#edit-category-modal .modal-title').html('Edit '+category.name+' Category');

			$('#edit-category-modal').modal('show');
		},

		showCreateModal: function() {

			//clear previous category model values
			$.each(app.models.category, function(i,v) {
				if (typeof(v) == "function") { v('') }
				
			});

			$('#edit-category-modal .modal-title').html('Create New Category');

			$('#edit-category-modal').modal('show');
		},

		/**
		 * Send request to server to create a new category.
		 * 
		 * @param  DOM form
		 * @return void
		 */
		create: function(form) {
			var self = app.viewModels.categories;

			app.utils.disablePage();
	
			app.utils.ajax({
                data: ko.toJSON(app.models.category),
                url: form.action,
                success: function(resp) {
                   location.reload();
                },
                error: function(resp) {
                	app.utils.enablePage();
                	app.utils.noty(resp.responseJSON, 'error');
                }
            });        
		},

		/**
		 * Delete a category.
		 * 
		 * @param  DOM category
		 * @return void
		 */
		delete: function(category) {
			var self = app.viewModels.categories;

			app.utils.disablePage();
	
			app.utils.ajax({
                data: ko.toJSON(app.models.category),
              	url: vars.urls.baseUrl+'/'+'categories/'+category.id,
              	type: 'DELETE',
                success: function(resp) {
                    app.utils.enablePage();
                    app.utils.noty(resp, 'success');
                   	self.sourceItems.remove(category);

                   	if (self.active() == category) {
                   		self.active(false);
                   	}
                },
                error: function(resp) {
                	app.utils.enablePage();
                	app.utils.noty(resp.responseJSON, 'error');
                }
            });

            return false;    
		},

		resizeImages: function() {
			var biggest = 410,
				images  = $('.category-body .img-responsive');
				
			for (var i = 0; i < images.length; i++) {
				if (images[i].height > biggest) {
					biggest = images[i].height;
				}
			};

			images.height(biggest);
		},

		/**
		 * Uri to hit for paginated results.
		 * 
		 * @type {String}
		 */
		uri: 'categories',

		start: function() {
			var self = this;
			
			app.paginator.start(app.viewModels.categories, '.content', 15).success(function() {
				self.setActiveCategory(self.sourceItems()[0]);
			});
			
		}
	};

	ko.bindingHandlers.hideShow = {
        init: function (element, valueAccessor, allBindingsAccessor, context) {

            var $container = $('#category-search .autocomplete-container');
			
            $('#category-search input').focus(function() {
            	console.log('a');
                if (app.viewModels.categories.autocompleteResults().length > 0) {
                    $container.fadeIn();
                }
            }).blur(function() {
                $container.fadeOut();
            });
        }
    };

	/**
     * Send autocomplete request to server on query value change.
     * 
     * @return void
     */
    app.viewModels.categories.fetchTitles = ko.computed(function() {
        var self   = this,
            query  = self.query(),
            type   = self.queryType(),
            middle = type == 'actor' ? '/typeahead-actor/' : '/typeahead/';


            //bail if query is falsy
            if ( ! query) return;

            self.autocompleteResults.removeAll();

            app.utils.ajax({
                url: vars.urls.baseUrl + middle + query,
                data: type === 'actor' ? JSON.stringify({ _token: vars.token }) : { _token: vars.token },
                type: type == 'actor' ? 'POST' : 'GET',
                success: function(data) {
                    self.autocompleteResults(data);
                }
            });

    }, app.viewModels.categories).extend({ rateLimit: { method: "notifyWhenChangesStop", timeout: 400 } });

    app.models.category = {
    	name: ko.observable(''),
    	icon: ko.observable('fa fa-fire'),
    	active: ko.observable(1),
    	weight: ko.observable(1),
    	showTrailer: ko.observable(1),
    	showRating: ko.observable(0),
    	autoUpdate: ko.observable(0),
    	query: ko.observable('popularTitles'),
    	limit: ko.observable(8),
    	_token: vars.token,
    };

})(jQuery);