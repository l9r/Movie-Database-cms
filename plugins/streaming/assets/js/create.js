(function($) {
	'use strict'

    /**
     * Map title object from serverside to js.
     * 
     * @return void
     */
    app.viewModels.titles.create.map = function() {
        
        $.each(vars.title, function(i,v) {
            if (i == 'writer' || i == 'director') {
                
                $.each(v, function(ind,val) {
                    app.models.title[i+'s'].push(val.name);
                });

            } else if (i == 'image' || i == 'link') {

                app.models.title[i+'s'] = ko.observableArray(v);

            } else if (i == 'actor') {

                app.models.title.actors = ko.observableArray(
                    $.map(v, function(actor) {
                        return { id: actor.id, name: actor.name, image: actor.image, char_name: actor.pivot.char_name };
                    })
                );

            } else if (i == 'season') {
                app.models.title.seasons(v);
            } else {
                app.models.title[i] = v;
                $('#'+i).val(v);
            }
        });
    };

    /**
     * Attach a new link to the title.
     *
     * @return void
     */
    app.viewModels.titles.create.addLink = function() {
        var self   = this,
            exists = false,
            link   = app.models.link;

        //if user pasted in iframe code we'll extract only the source
        if (link.url().indexOf("iframe") > -1) {
        	var url = link.url().match(/src="([^"]+)"/);
        	if (!url) url = link.url().match(/src='([^']+)'/);
            app.models.link.url(url[1]);
        }

        app.models.link.title_id = app.models.title.id || vars.titleId;

        app.utils.ajax({
            url: vars.urls.baseUrl+'/links/attach',
            data: ko.toJSON(app.models.link),
            success: function(data) {

                if (app.models.link.id) {
                    $.each(app.models.title.links(), function(i,v) {
                        if (v && v.id == app.models.link.id) {
                            app.models.title.links.remove(v);
                            return false;
                        }
                    });
                } else {
                    app.models.link.id = data.id;
                }

                if ( ! parseInt(data.approved)) {
                    app.utils.noty(vars.trans.userLinkAdded, 'success');
                    $('#add-link-modal').modal('hide');
                }

                app.models.title.links.push(ko.toJS(app.models.link));
                app.models.link.url('');
                app.models.link.label('');
                app.models.link.id = null;
            },
            error: function(data) {
                app.utils.noty(data.responseJSON, 'error');
            }
        });

    };

    /**
     * Hydrate link model with provided values.
     * 
     * @param  object link
     * @return void
     */
    app.viewModels.titles.create.editLink = function(link) {
        app.models.link.url(link.url);
        app.models.link.type(link.type);
        app.models.link.label(link.label);
        app.models.link.season(link.season);
        app.models.link.episode(link.episode);
        app.models.link.id = link.id;

        window.scrollTo(0,0);
    };

    /**
     * Detach a link from title.
     *
     * @return void
     */
    app.viewModels.titles.create.removeLink = function(link) {
        $.each(app.models.title.links(), function(i,v) {
            if (v && v.url == link.url) {
                app.models.title.links.remove(v);
            }
        });

        if (vars.title || vars.titleId) {
            app.utils.ajax({
                url: vars.urls.baseUrl + '/links/detach',
                data: ko.toJSON({ _token: vars.token, title_id: app.models.title.id || vars.titleId, url: link.url }),
            });
        }       
    };

    app.models.title = {
        actors: ko.observableArray().extend({ rateLimit: 50 }),
        images: ko.observableArray([]).extend({ rateLimit: 50 }),
        directors: ko.observableArray(),
        writers: ko.observableArray(),
        seasons: ko.observableArray(),
        links: ko.observableArray(),
    };

    app.models.link = {
        label: ko.observable(),
        url : ko.observable(),
        type: ko.observable(),
        _token: vars.token,
        reports: 0, 
        season: ko.observable(),
        episode: ko.observable(),
    };

})(jQuery);
