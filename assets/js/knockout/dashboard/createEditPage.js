(function($) {
	'use strict'

	app.viewModels.createEditPage = {

		/**
		 * The name of tag user is attaching to this item.
		 * 
		 * @type ko.observable(String)
		 */
		newTag: ko.observable(),

		/**
		 * Type of item we're working with currently.
		 * 
		 * @type String
		 */
		type: ko.observable('page'),

		/**
		 * If true we are creating a new page, otherwise editing existing one.
		 * 
		 * @type {boolean}
		 */
		creating: ko.observable(true),

		/**
		 * Send a request to server to save the
		 * item and publish it.
		 * 
		 * @param  DOM form
		 * @return void
		 */
		publish: function(form) {
			var self = app.viewModels.createEditPage;
			
			app.utils.ajax({
				url: form.action,
				type: vars.method ? vars.method : 'POST',
				data: self.getData(),
				success: function(data) {
					app.utils.noty(data, 'success');
					$('.alert').remove();
							
					if (self.creating() && self.type() === 'page') {
						window.location.replace(vars.urls.dashPages);
					}
				},
				error: function(jq) {
					$('.alert').remove();
					
					if (typeof jq.responseJSON == 'string' || jq.responseJSON instanceof String) {
						app.utils.noty(jq.responseJSON, 'error');
					} else {
						$.each(jq.responseJSON, function(i,v) {
							app.utils.appendError(v[0], 'form .col-sm-9', 'prepend');
						})
					} 
				},
			})
		},

		/**
		 * Get form data to send to server.
		 * 
		 * @return JSON
		 */
		getData: function() {

			//decide which model to send serverside depending on
			//the type of item we're working with
			if (app.viewModels.createEditPage.type() == 'news') {
				var model = app.models.newsItem;
			} else if (app.viewModels.createEditPage.type() == 'review') {
				var model = app.models.review;
				model.game_id = vars.game.id;
			} else {
				var model = app.models.page;
			}

			//get ckeditor content manually if user tries to save without trigger
			//blur event and models body hasn't updated
			if ( ! model.body() || model.body().length < 50) {
				model.body(CKEDITOR.instances.editor.getData());
			}

			// if (app.display_name()) {
			// 	model.author = app.display_name();
			// }

			return ko.toJSON(model);
		},

		/**
		 * Attach new tag to news item.
		 *
		 * @return void
		 */
		addNewTag: function() {
			var self   = this,
				newTag = self.newTag;

			if (newTag() && ! app.utils.inArray(app.models.newsItem.tags(), newTag())) {
				app.models.newsItem.tags.push(self.newTag());
			}

			//clear the new tag input field
			self.newTag('');
		},

		/**
		 * Remove passed tag from allTags array.
		 * 
		 * @param  String tag
		 * @return void
		 */
		removeTag: function(tag) {
			app.models.newsItem.tags.remove(tag);
		},

		start: function(type) {

			if (type && type == 'news') {
				app.viewModels.createEditPage.type('news');

				//set draft/public checkbox state
		        if (app.models.newsItem.active()) {
		        	$('#radio-public').iCheck('check');
		        } else {
		        	$('#radio-draft').iCheck('check');
		        }

			} else if (type == 'review') {
				app.viewModels.createEditPage.type('review');
			} 
			else {

				//set draft/public checkbox state
		        if (app.models.page.visibility() == 'public') {
		        	$('#radio-public').iCheck('check');
		        } else {
		        	$('#radio-admin').iCheck('check');
		        }

			}

			app.viewModels.media.ckeExists(true);
			app.viewModels.media.start();
			app.paginator.start(app.viewModels.media, $('#upload-media-modal')[0], 24);	
			ko.applyBindings(app.viewModels.createEditPage, $('#form')[0]);

			var $radio = $('[name="status"], [name="type"]');

			$radio.on('ifChecked', function(e) {
				var val = e.target.defaultValue;

				if (type == 'news') {
	            	app.models.newsItem.active(val);
	            } else if (type == 'review') {
	            	app.models.review.type(val);
	            } 
	            else {
	            	app.models.page.visibility(val);
	            }
	        });
		}
	};

	/**
	 * page model.
	 * 
	 * @type {Object}
	 */
	app.models.page = {
		title: ko.observable(),
	    body: ko.observable(),
	    slug: ko.observable(),
		visibility: ko.observable('public'),
		_token: vars.token,
	};

	/**
	 * News item model.
	 * 
	 * @type {Object}
	 */
	app.models.newsItem = {
		title: ko.observable(),
	    body: ko.observable(),
	    image: ko.observable(),
	    active: ko.observable(0),
		tags: ko.observableArray(),
		_token: vars.token,
	};

	/**
	 * review model.
	 * 
	 * @type {Object}
	 */
	app.models.review = {
		title: ko.observable(),
	    body: ko.observable(),
	    rating: ko.observable(),
	    platform: ko.observable(),
	    type: ko.observable(),
	    fully_scraped: 1,
		_token: vars.token,
	};


	/**
	 * Prevent form submit when pressing enter on element.
	 * 
	 * @type {Object}
	 */
	ko.bindingHandlers.preventSubmitOnEnter = {
	    init: function(element, valueAccessor, allBindings, context) {
	        $(element).keydown(function(e) {
	            if (e.keyCode === 13) {
	                e.preventDefault();
	                app.viewModels.createEditPage.addNewTag();

	                return false;
	            }       
	        });
	    }
	}

	/**
	 * Renders CKeditor on textarea.
	 * 
	 * @type {Object}
	 */
	ko.bindingHandlers.ckeditor = {
	    init: function (element, valueAccessor, allBindingsAccessor, context) {
	        var $element = $(element);
	        var value = ko.utils.unwrapObservable(valueAccessor());

	        $element.html(value);
	        var editor = CKEDITOR.replace('editor');

	        /**
	         * Resize CKeditor according to textarea col and rows attributes.
	         * 
	         * @return void
	         */
	        jQuery.fn.cke_resize = function() {
	           return this.each(function() {
	              var $this = $(this);
	              var rows = $this.attr('rows');
	              var height = rows * 20;
	              $this.next("div.cke").find(".cke_contents").css("height", height);
	           });
	        };
	        
	        CKEDITOR.on('instanceReady', function(){ $element.cke_resize(); });
	     
	        //Update body observable on ckeditor blur event
	        editor.on('blur', function (e) {

	        	//decide which model to use depending on
				//the type of item we're working with
				if (app.viewModels.createEditPage.type() == 'news') {
					var obs = app.models.newsItem.body;
				} else {
					var obs = app.models.page.body;
				}
	           
	            if (ko.isWriteableObservable(obs)) {
	                obs(e.editor.getData());
	            }
	        });
	    }
	};

})(jQuery);