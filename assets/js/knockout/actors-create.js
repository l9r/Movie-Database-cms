(function($) {
	'use strict'

    app.viewModels.actorsCreate = {
       

        /**
         * Make actor known for a title.
         * 
         * @param  DOM form
         * @return void
         */ 
        knownFor: function(form) {
            app.utils.ajax({
                url: vars.urls.baseUrl+'/people/knownFor',
                data: $(form).getFormData(),
                success: function(data) {
                    app.utils.noty(data, 'success');
                }
            });
        },

        /**
         * Send request to server to save actor.
         * 
         * @return void
         */
        save: function() {
            var self = this,
                url  = vars.urls.baseUrl+'/'+vars.people;

            app.utils.disablePage();

            app.utils.ajax({
                type: vars.actor ? 'PUT' : 'POST',
                data: $('#main-form').getFormData(),
                url: vars.actor ? url+'/'+vars.actor.id : url,
                success: function(resp) {
                    app.utils.enablePage();
                    app.utils.noty(resp, 'success');
                },
                error: function(resp) {
                    app.utils.enablePage();

                    $('.help-block').remove();
                    $('.form-group').removeClass('has-error');
                    
                    if (resp.responseJSON instanceof String) {
                        app.utils.noty(resp, 'error');
                    } else {
                        $.each(resp.responseJSON, function(i,v) {
                            var $el = $('#'+i);

                            $el.after('<div class="help-block">'+v+'</div>');
                            $el.parent().addClass('has-error');
                        })
                    }
                   
                }
            });        
        },

        detachTitle: function(title) {

            app.utils.ajax({
                url: vars.urls.baseUrl+'/'+vars.people+'/'+'unlink',
                data: ko.toJSON({ _token: vars.token, title_id: title.id, actor_id: vars.actor.id }),
                success: function(data) {
                    app.utils.noty(data, 'success');
                }
            });
        },

        /**
         * Map actors object from serverside to js.
         * 
         * @return void
         */
        map: function() {
            var $form = $('#main-form');
            
            $.each(vars.actor, function(i,v) {

                if (i == 'title') {
                    vars.filmo = ko.observableArray(v);
                } else {
                    $('#main-form #'+i).prop('value', v); 
                }
               
            });
        },

        /**
         * Innitiate the view model.
         * 
         * @return void
         */
        start: function() {
            
            ko.applyBindings(app.viewModels.actorsCreate, $('#content')[0]);
            app.viewModels.media.start();
            app.paginator.start(app.viewModels.media, $('#upload-media-modal')[0], 24);
        },

    };

    /**
     * Send autocomplete request to server on query value change.
     * 
     * @return void
     */
    app.viewModels.titles.create.fetchCast = ko.computed(function() {
        var self  = this,
            query = app.viewModels.titles.create.castQuery();

            //bail if query is falsy
            if ( ! query) return;

            app.utils.ajax({
                url: vars.urls.baseUrl + '/typeahead-actor/' + query,
                data: ko.toJSON({_token:vars.token}),
                success: function(data) {
                    self.castResults(data);
                }
            });

    }, app.viewModels.titles.create).extend({ rateLimit: { method: "notifyWhenChangesStop", timeout: 400 } });

    app.models.title = {
        actors: ko.observableArray().extend({ rateLimit: 50 }),
        images: ko.observableArray([]).extend({ rateLimit: 50 }),
        directors: ko.observableArray(),
        writers: ko.observableArray(),
        seasons: ko.observableArray(),
    };

    app.models.episodeForm = {
        _token: vars.token,
        title: ko.observable(),
        release_date: ko.observable(),
        poster: ko.observable(),
        plot: ko.observable(),
        promo: ko.observable(),
        episode_number: ko.observable(),
    };


})(jQuery);
