(function($) {
	'use strict'

	app.viewModels.users = {

		/**
		 * Users that's currently being edited username.
		 * 
		 * @type mixed
		 */
		username: ko.observable(false),

		/**
		 * Holds all titles.
		 * 
		 * @type ko.observable(Array)
		 */
		sourceItems: ko.observableArray(),

		/**
		 * Populate modal with existing users data.
		 * 
		 * @param  mixed id 
		 * @return void
		 */
		populateModal: function(id) {
			var f = $('#new-user-modal form');

			$.each(app.viewModels.users.sourceItems(), function(i,user) {
				if (user.id === id) {
					f.find('[name=username]').val(user.username);
					f.find('[name=email]').val(user.email);
					f.find('[name=avatar]').val(user.avatar);
					f.find('[name=gender]').val(user.gender);
					f.find('[name=permissions]').val(user.permissions);

					//clear any error fields
					$('.help-block').remove();
                    $('.form-group').removeClass('has-error');

					$('#new-user-modal').modal('show');
					app.viewModels.users.username(user.username);
					return false;
				}
			});
		},

		/**
		 * Send request to server to create a new user.
		 * 
		 * @param  DOM form
		 * @return void
		 */
		create: function(form) {
			var self = app.viewModels.users;
	
			app.utils.ajax({
                data: $(form).getFormData(),
                type: self.username() ? 'PUT' : 'POST',
                url: self.username() ? vars.urls.baseUrl+'/'+vars.trans.users+'/'+self.username() : form.action,
                success: function(resp) {
                    app.utils.enablePage();
                    app.utils.noty(resp, 'success');

                    self.username(false);
                    $('#new-user-modal').modal('hide');
                },
                error: function(resp) {
                    app.utils.enablePage();

                    $('.help-block').remove();
                    $('.form-group').removeClass('has-error');
                    
                    if (resp.responseJSON instanceof String) {
                        app.utils.noty(resp, 'error');
                    } else {
                        $.each(resp.responseJSON, function(i,v) {
                            var $el = $('[name="'+i+'"]');

                            $el.after('<div class="help-block">'+v+'</div>');
                            $el.parent().addClass('has-error');
                        })
                    }
                   
                }
            });        
		},

		/**
		 * Register any needed events.
		 * 
		 * @return void
		 */
		registerEvents: function() {
			$('#new-user-modal').on('hidden.bs.modal', function (e) {
				app.viewModels.users.username(false);

				var f = $('#new-user-modal form');

				f.find('[name=username]').val('');
				f.find('[name=email]').val('');
				f.find('[name=avatar]').val('');
				f.find('[name=permissions]').val('');
			})
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
		uri: 'users',
	};

})(jQuery);