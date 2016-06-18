(function($) {
	'use strict';

    app.viewModels.titles.create = {

        /**
         * Holds cast autocomplete query results.
         * 
         * @type ko.observable(Array)
         */
        castResults: ko.observableArray(),

        /**
         * autocomplete query.
         * 
         * @type string
         */
        castQuery: ko.observable(),

        /**
         * The name of director user is attaching to the title.
         * 
         * @type string
         */
        newDirector: ko.observable(),

        /**
         * The name of writer user is attaching to the title.
         * 
         * @type string
         */
        newWriter: ko.observable(),

        /**
         * Season user is current editing if any.
         * 
         * @type Object
         */
        activeSeason: ko.observable(),

        /**
         * Episode user is current editing if any.
         * 
         * @type Object
         */
        activeEpisode: ko.observable(),

        /**
         * Send request to server to save the title and relations.
         * 
         * @return void
         */
        save: function() {
            var self = this,
                data = $('#main-form').serializeArray();

            app.utils.disablePage();

            //get the main values from form here as we're not using
            //observables there
            $.each(data, function(i,v) {
                if (v.value) { app.models.title[v.name] = v.value; }
            });

            app.utils.ajax({
                data: ko.toJSON(app.models.title),
                url: $('#main-form').prop('action'),
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

        getData: function() {
            app.utils.disablePage();

             var imdbId = document.getElementById('imdb_id').value,
                 tmdbId = document.getElementById('tmdb_id').value,
                 type   = document.getElementById('type').value,
                 self   = this;

             if (tmdbId) {
                 var provider = 'tmdb', id = tmdbId;
             } else if (imdbId) {
                 var provider = 'imdb', id = imdbId;
             } else {
                 app.utils.enablePage();
                 return app.utils.noty('You need to enter either IMDb or themoviedb id to fetch data', 'error');
             }

             app.utils.ajax({
                 url: vars.urls.baseUrl+'/title-data/'+type+'/'+provider+'/'+id,
                 data: ko.toJSON({ _token: vars.token }),
                 success: function(resp) {
                     app.utils.enablePage();
                     self.map(resp);
                     app.utils.noty(vars.trans.importSuccess, 'success');
                 },
                 error: function() {
                     app.utils.enablePage();
                     app.utils.noty(vars.trans.importFail, 'error');
                 }
             });

        },

        /**
         * Map title object from server to js
         *
         * @param {object|undefined} data
         */
        map: function(data) {
            $.each(data || vars.title, function(i,v) {
                if (i == 'writer' || i == 'director') {
                    
                    $.each(v, function(ind,val) {
                        app.models.title[i+'s'].push(val.name);
                    });

                } else if (i == 'image') {
                    ko.utils.arrayPushAll(app.models.title.images, v);
                } else if (i == 'actor') {
                    var actors = $.map(v, function(actor) {
                        return { id: actor.id, name: actor.name, image: actor.image, char_name: actor.pivot.char_name };
                    });

                    ko.utils.arrayPushAll(app.models.title.actors, actors);
                } else if (i == 'season') {
                    app.models.title.seasons(v);
                } else {
                    app.models.title[i] = v;
                    $('#'+i).val(v);
                }
            });
        },

        /**
         * Attach a new season to series.
         */
        addSeason: function() {
            var self = this,
                count = app.models.title.seasons().length;

            var season = { 
                _token: vars.token, 
                title: 'Season '+(count+1), 
                number: count+1, 
                title_id: app.models.title.id,
                title_tmdb_id: app.models.title.tmdb_id,
                title_imdb_id: app.models.title.imdb_id,
            };

            app.utils.ajax({
                url: vars.urls.baseUrl+'/'+vars.trans.series+'/'+vars.title.id+'/seasons',
                data: ko.toJSON(season),
                success: function(data) {
                    app.utils.noty(data.message, 'success');

                    season.episode = [];
                    season.id = data.id;

                    app.models.title.seasons.push(season);
                    self.activeSeason(season);
                }
            });
        },

        showEpisodeModal: function() {
            var self = this;

            $('#edit-ep-modal').modal('toggle');
            self.emptyEpModel();
        },

        /**
         * Attach new episode to season or update existing one.
         */
        addEpisode: function() {
            var self = this,
                exists = false;

            //attach some more needed details to episode model
            app.models.episodeForm.title_id = app.models.title.id;
            app.models.episodeForm.season_id = self.activeSeason().id;
            app.models.episodeForm.season_number = self.activeSeason().number;

            app.utils.ajax({
                url: vars.urls.baseUrl+'/'+vars.trans.series+'/'+vars.title.id+'/seasons/'+self.activeSeason().id+'/episodes',
                data: ko.toJSON(app.models.episodeForm),
                success: function(data) {
                    app.utils.noty(data, 'success');
                    $('#edit-ep-modal').modal('toggle');

                    //check if we've modified an existing episode first
                    $.each(self.activeSeason().episode, function(i,v) {
                        if (v.episode_number == app.models.episodeForm.episode_number()) {
                            exists = true;
                            self.activeSeason().episode[i] = ko.toJS(app.models.episodeForm);

                            return false;
                        }
                    });

                    if ( ! exists) {
                        self.activeSeason().episode.unshift(ko.toJS(app.models.episodeForm));
                    }
                    
                    self.activeSeason.valueHasMutated();
                    self.emptyEpModel();

                },
                error: function(data) {
                    $('.help-block').remove();
                    $('.form-group').removeClass('has-error');

                    if (data.responseJSON instanceof String) {
                        app.utils.noty(data, 'error');
                    } else {
                        $.each(data.responseJSON, function(i,v) {
                            var $el = $('#edit-ep-modal #'+i);

                            $el.after('<div class="help-block">'+v+'</div>');
                            $el.parent().addClass('has-error');
                        })
                    }
                }
            });
        },

        /**
         * Empty episode modal form and model.
         * 
         * @return void
         */
        emptyEpModel: function() {
            $.each(app.models.episodeForm, function(i,v) {
                if (ko.isObservable(v)) {
                    v(null);
                }
            });
        },

        /**
         * Set passed season as the one user is editing currently.
         * 
         * @param Object season
         */
        setActiveSeason: function(season) {
            app.viewModels.titles.create.activeSeason(season);
        },

        /**
         * Remove the specified episode from current active season.
         * 
         * @param  object episode
         * @return void
         */
        removeEpisode: function(episode) {
            var self = app.viewModels.titles.create;

            app.utils.ajax({
                data: ko.toJSON({ _token: vars.token }),
                type: 'DELETE',
                url: vars.urls.baseUrl+'/'+vars.trans.series+'/'+vars.title.id+'/seasons/'+self.activeSeason().id+'/episodes/'+episode.id,
                success: function(resp) {
                    app.utils.noty(resp, 'success');

                    //remove the episode from front end
                    $.each(self.activeSeason().episode, function(i,v) {
                        if (episode.id == v.id) {
                            self.activeSeason().episode.splice(i, 1);
                            self.activeSeason.valueHasMutated();

                            return false;
                        }
                    });       
                }
            });
        },

        /**
         * Delete current active season from db and front end.
         * 
         * @return void
         */
        deleteSeason: function() {
            var self = this;

            app.utils.ajax({
                data: ko.toJSON({ _token: vars.token }),
                type: 'DELETE',
                url: vars.urls.baseUrl+'/'+vars.trans.series+'/'+vars.title.id+'/seasons/'+self.activeSeason().id,
                success: function(resp) {
                    app.utils.noty(resp, 'success');

                    //remove the episode from front end
                    $.each(app.models.title.seasons(), function(i,v) {
                        if (self.activeSeason().title == v.title) {
                            app.models.title.seasons.remove(v);

                            return false;
                        }
                    });       
                }
            });
        },

        /**
         * Show the modal to edit an episode.
         * 
         * @param  object episode
         * @return void
         */
        editEpisode: function(episode) {
            var self = app.viewModels.titles.create;

            app.viewModels.titles.create.activeEpisode(episode);

            //fill episode form model with passed episodes values
            $.each(episode, function(i,v) {
                if (ko.isObservable(app.models.episodeForm[i])) {
                    app.models.episodeForm[i](v);
                } else {
                    app.models.episodeForm[i] = v;
                }
            });

            //show the modal
            $('#edit-ep-modal').modal('toggle');
        },

        /**
         * Attach an actors to title.
         * 
         * @param object
         */
        addActor: function(actor) {
            var exists = false;

            //check if actor doensn't exist in cast already
            $.each(app.models.title.actors(), function(i,v) {

                if (v.name == actor.name) {
                    exists = true;
                    return false;
                }
            });

            if ( ! exists) {

                //add char_name property now so we render and bind
                //input element in the UI for it.
                actor.char_name = ko.observable();

                app.models.title.actors.unshift(actor);
            }
           
            $('#cast .autocomplete-container').hide();
        },

        /**
         * Remove actor from title.
         *
         * @return void
         */
        removeActor: function(actor) {
            app.models.title.actors.remove(actor);
            
            //if we're editing an existing title send a request to
            //server to remove the relevant record from pivot table
            if (vars.title) {
                app.utils.ajax({
                    url: vars.urls.baseUrl + '/detach-people',
                    data: ko.toJSON({ _token: vars.token, titleId: vars.title.id, resourceId: actor.id, type: 'actor' }),
                });
            }       
        },

        /**
         * Attach director to the title.
         *
         * @return void
         */
        addDirector: function() {
            var self   = this,
                dir = self.newDirector();

            if (dir && ! app.utils.inArray(app.models.title.directors(), dir)) {
                app.models.title.directors.push(dir);
            }

            //clear the new director input field
            self.newDirector('');
        },

        /**
         * Remove director from title.
         *
         * @return void
         */
        removeDirector: function(director) {
            app.models.title.directors.remove(director);
            
            //if we're editing an existing title send a request to
            //server to remove the relevant record from pivot table
            if (vars.title) {
                app.utils.ajax({
                    url: vars.urls.baseUrl + '/detach-people',
                    data: ko.toJSON({ _token: vars.token, titleId: vars.title.id, resourceName: director, type: 'director' }),
                });
            }       
        },

        /**
         * Attach writer to the title.
         *
         * @return void
         */
        addWriter: function() {
            var self   = this,
                writer = self.newWriter();

            if (writer && ! app.utils.inArray(app.models.title.writers(), writer)) {
                app.models.title.writers.push(writer);
            }

            //clear the new writer input field
            self.newWriter('');
        },

        /**
         * Remove writer from title.
         *
         * @return void
         */
        removeWriter: function(writer) {
           app.models.title.writers.remove(writer);

           //if we're editing an existing title send a request to
            //server to remove the relevant record from pivot table
            if (vars.title) {
                app.utils.ajax({
                    url: vars.urls.baseUrl + '/detach-people',
                    data: ko.toJSON({ _token: vars.token, titleId: vars.title.id, resourceName: writer, type: 'writer' }),
                });
            }  
        },

        /**
         * Remove image from title model.
         * 
         * @param  object image
         * @return void
         */
        removeImage: function(image) {
            app.models.title.images.remove(image);

            app.utils.ajax({
                url: vars.urls.baseUrl + '/media/' + image.id,
                data: ko.toJSON({ _token: vars.token }),
                type: 'DELETE',
            });
        },

        /**
         * Show and hide autocomplet results on blur and focus events.
         * 
         * @return void
         */
        showHideAutocomplete: function() {
            var self  = this,
                $cont = $('#cast .autocomplete-container');

            $('#cast-query').focus(function() {
                if (self.castResults().length > 0) {
                    $cont.fadeIn();
                }
            }).blur(function() {
                $cont.fadeOut();
            });
        },

        /**
         * Innitiate the view model.
         * 
         * @return void
         */
        start: function() {
            app.viewModels.titles.create.showHideAutocomplete();
            ko.applyBindings(app.viewModels.titles.create, $('#content')[0]);

            app.viewModels.media.editingTitle(true);
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
