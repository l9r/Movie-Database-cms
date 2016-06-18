(function($) {
	'use strict';

    app.viewModels.titles.show = {

        /**
         * Whether or not user has added this title to watchlist.
         * 
         * @type boolean
         */
        watchlist: ko.observable(false),

        /**
         * Whether or not user has added this title to favorites.
         * 
         * @type boolean
         */
        favorite: ko.observable(false),

        userId: ko.observable(false),

        lazyLoadImage: function(item) {
            $(item).find('img').lazyload();
        },

        handleLists: function(name) {
            var self = app.viewModels.titles.show,
                alreadyAdded = self[name]();

            app.utils.ajax({
                url: alreadyAdded ? vars.urls.baseUrl + '/lists/remove' : vars.urls.baseUrl + '/lists/add',
                data: ko.toJSON({ _token: vars.token, list_name: name, title_id: vars.titleId }),
                success: function(data) {
                    if (alreadyAdded) { 
                        self[name](false);
                    } else {
                        self[name](true);
                    }  
                }
            })
        },

        showTrailer: function() {
            var $mask = $('#trailer-mask');

            if (vars.trailersPlayer == 'default') {
                $mask.html('<div class="embed-responsive embed-responsive-16by9"><iframe class="embed-responsive-item" src="'
                    +$mask.data('src')+'?autoplay=1&iv_load_policy=3&modestbranding=1&rel=0" wmode="opaque" allowfullscreen="true"></iframe></div>');
            } else {
                 //set up either to play from youtube or mp4 file
                if ($mask.data('src').indexOf('youtube') != -1) {
                    videojs('trailer', { "techOrder": ["youtube"]}).src($mask.data('src')).play();
                }
                else {
                    videojs('trailer', { "techOrder": ["html5", "flash"]}).src($mask.data('src')).play();
                }

                $mask.css('display', 'none');
                $('#trailer').css('display', 'block');

                //reposition social and lists buttons once video is shown
                $('#social').css('top', 0).css('left', 0);
                $('#lists').css('top', 0).css('right', 0);

                //show/hide social and list buttons when player controls are shown/hidden
                videojs('trailer').on('userinactive', function() {
                    $('#social').css('display', 'none');
                    $('#lists').css('display', 'none');
                });

                videojs('trailer').on('useractive', function() {
                    $('#social').css('display', 'block');
                    $('#lists').css('display', 'block');
                });
            }
        },

        start: function() {
            var self = app.viewModels.titles.show;

            self.userId(vars.userId);

            //see if user has already added this title to favorites or watchlist
            if (vars.lists) {
                $.each(vars.lists, function(i,v) {
                    if (v.title_id == vars.titleId && v.watchlist) {
                        self.watchlist(true);
                    }

                    if (v.title_id == vars.titleId && v.favorite) {
                        self.favorite(true);
                    }
                });
            }

            app.startGallery();

            var h = $('#details-container').height();
            $('#details-container .img-responsive').height(h);

            app.loadDisqus();
        },

        reviews: app.viewModels.reviews,
    }

})(jQuery);
