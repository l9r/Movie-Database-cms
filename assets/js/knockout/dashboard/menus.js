(function($) {
	'use strict'

	app.viewModels.menus = {

		/**
		 * Holds menu that's currently being edited.
		 * 
		 * @type ko.observable(Object)
		 */
		activeMenu: ko.observable(),

		/**
		 * Holds all existing menus.
		 * 
		 * @type ko.observable(Array)
		 */
		allMenus: ko.observableArray(),

		/**
		 * Routes that are currently selected by user.
		 * 
		 * @type ko.observable(Array)
		 */
		selectedRoutes: ko.observableArray(),

		/**
		 * Partial user currently selected to attach to a menu item.
		 *
		 * @type ko.observable(String)
		 */
		selectedPartial: ko.observable(),

		/**
		 * Page user currently selected to attach to a menu item.
		 * 
		 * @type ko.observable(String)
		 */
		selectedPage: ko.observable(),

		/**
		 * Menu item we are currently attaching (sub)items to.
		 * 
		 * @type ko.observable
		 */
		attachingTo: ko.observable(),

		/**
		 * Map menus object to menus model.
		 * 
		 * @param  Object menus
		 * @return Array
		 */
		map: function(menus) {
			var self = this;
			var mapped = [];

			if (menus) {
				$.each(menus, function(i, menu) {
					var mappedMenu = {}; 

					//convert main menu attributes to observables
					mappedMenu.name = ko.observable(menu.name);
					mappedMenu.position = ko.observable(menu.position);
					mappedMenu.active = ko.observable(menu.active);

					//convert menu item attributes to observables
					var mappedItems = ko.observableArray(
						ko.utils.arrayMap(menu.items, function(item) {
							
							//if menu item has sub-items convert the sub items to observables as well
							if (item.children[0]) {
								item.children = ko.utils.arrayMap(item.children, function(child) {
									return new app.models.menuItem(child);
								});
							}

							return new app.models.menuItem(item);
						})
					);
					
					mappedMenu.items = mappedItems;			 
					mapped.push(mappedMenu);
				});
			}

			return mapped;
		},

		/**
		 * Handles new menu creation.
		 * 
		 * @return void
		 */
		createNew: function() {
			var self   = this,
				exists = false;

			//check if menu with same name doesn't exist already
			$.each(self.allMenus(), function(i, v) {
				if (v.name() === app.models.menu.name()) {		
					exists = true;
				}
			});
		
			if ( ! exists) {

				//unwrap model observbable and push values to another
				//observable so we can use the same model object for creating
				//multiple menus and they are not all linked
				var menu = {
					name: ko.observable(app.models.menu.name()),
					position: ko.observable(app.models.menu.position()),
					items: ko.observableArray([]),
				};

				//push new menu to all menus array and set it as active
				self.allMenus.push(menu);
				self.activeMenu(menu);

				//clear new menu form fields
				app.models.menu.name('');		
				app.models.menu.position('');
			}		

			//hide create new menu container
			$('#create-new-cont').collapse('hide');
		},

		/**
		 * Delete a menu currently selected as active.
		 * 
		 * @return void
		 */
		deleteMenu: function() {
			var self = this;

			if (self.activeMenu && self.activeMenu()) {
				self.allMenus.remove(self.activeMenu());
			}
		},

		/**
		 * Save menus to database.
		 * 
		 * @return void
		 */
		save: function() {
			var self = this;

			app.utils.disablePage();

			if (self.activeMenu()) {
				app.utils.ajax({
					data: ko.toJSON({ _token: vars.token, menus: self.allMenus() }),
					url: vars.urls.baseUrl + '/dashboard/options',
					success: function(data) {
						app.utils.noty(data, 'success');
						app.utils.enablePage();
					},
					error: function(data) {
						app.utils.enablePage();
						app.utils.noty(data.responseJSON, 'error');
					}
				})
			}
		},

		/**
		 * Attaches selected routes to current active menu and
		 * matching menu in allMenus array.
		 * 
		 * @return void
		 */
		attachRoutes: function() {
			var self = this;

			$.each(self.selectedRoutes(), function(i,v) {
				var route = new app.models.menuItem({label: v, action: v, weight: i, type: 'route'});

				if (self.attachingTo()) {
					self.attachingTo().children.push(route);
				} else {
					self.activeMenu().items.push(route);
				}		
			});

			//uncheck all the checkboxes after attaching routes
			$('#routes input').iCheck('uncheck');
		},

		/**
		 * On add link button click attaches new link to current
		 * active menu and same menu in allMenus array so changes
		 * aren't lost if user changes active menu before saving.
		 *
		 * @return void
		 */
		attachLink: function(a, b) {
			var self   = this,
				tmp    = { type: 'link', label: 'Label' },
				inputs = $('#links input, #links select');
			 		
			//make value object that we can pass into menu item
			//constructor from link inputs
			$.each(inputs, function(i,v) {
				var inp = $(v);

				//assign input value to temp object
				if (inp.val()) {
					tmp[v.name] = inp.val();
				}
				
				//empty the input in the UI
				inp.val('');
			});

			var link = new app.models.menuItem(tmp);

			if (self.attachingTo()) {
				self.attachingTo().children.push(link);
			} else {
			 	self.activeMenu().items.push(link);
			}	
		},

		/**
		 * Attach selected page to active menu.
		 * 
		 * @return void
		 */
		attachPage: function() {
			var self = this,
				name = self.selectedPage();

			var page = new app.models.menuItem({
				label: name,
				action: name,
				weight: 1,
				type: 'page',
			});

			if (self.attachingTo()) {
				self.attachingTo().children.push(page);
			} else {
			 	self.activeMenu().items.push(page);
			}
		},

		/**
		 * Toggles menu item details when called.
		 * 
		 * @param  Object item
		 * @param  Object event
		 * @return void
		 */
		showPanelBody: function(item, event) {
			$(event.target).closest('.panel').find('.panel-body').slideToggle(300);
		}
		
	};

	/**
	 * New menu model.
	 * 
	 * @type {Object}
	 */
	app.models.menu = {
		name: ko.observable(),
		position: ko.observable(),
		active: ko.observable(),
		items: ko.observableArray([]),
	};

	/**
	 * Menu item model.
	 * 
	 * @param  Object data
	 * @return void
	 */
	app.models.menuItem = function(data) {
		console.log(data);
		this.label      = ko.observable(data.label);
		this.action     = ko.observable(data.action);
		this.weight     = ko.observable(data.weight);
		this.type       = ko.observable(data.type);
		this.partial    = ko.observable(data.partial);
		this.children   = ko.observableArray(data.children);
		this.visibility = ko.observable(data.visibility),

		this.removeLink = function(parent, child) {

			//we are removing a base menu item
			if (parent.activeMenu) {
				parent.activeMenu().items.remove(child);

			//we are removing a child menu item from base item
			} else {
				parent.children.remove(child);
			}
		};
	};

	/**
	 * Pretify formcontrols and push value to observable.
	 * 
	 * @type Object
	 */
	ko.bindingHandlers.iCheck = {

	    init: function (element, valueAccessor, allBindingsAccessor, context) {
	        $(element).iCheck({
	            radioClass:    'iradio_square-aero',
	            increaseArea:  '20%'
	        });

	        var $radio = $('[name="page"]');

	        $radio.on('ifChecked', function(e) {
	            var val = e.target.defaultValue;
	            app.viewModels.menus.selectedPage(val);
	        });
	    }
	};

	/**
	 * Inniate icheck plugin on checkbox and push/remove
	 * route object to/from array on check/uncheck.
	 * 
	 * @type {Object}
	 */
	ko.bindingHandlers.checkboxArray = {
	    init: function (element, values) {
    		var $el = $(element);

	        $el.iCheck({
	            checkboxClass: 'icheckbox_square-aero',
	            increaseArea: '20%'
	        });

	        $el.on('ifChecked', function() {
	            values().checked.push(values().label);
	        });

	        $el.on('ifUnchecked', function() {
	            values().checked.remove(values().label);
	        }); 
	    },
	};
})(jQuery);