(function($) {
	'use strict';

    app.viewModels.titles.show.playVideo = function(url, type) {
        var modal     = $('#video-modal'),
            body      = $('#video-container'),
            height    = $(window).height() - 50,
            isYoutube = url.indexOf('youtube') > -1 || url.indexOf('youtu.be') > -1;

        modal.off('hide.bs.modal').on('hide.bs.modal', function (e) {
            body.html('');

            if (document.getElementById('trailer')) {
                videojs('trailer').dispose();
            }
        });
            
        if (type == 'embed' || ( ! type && vars.trailersPlayer == 'default')) {
            body.html('<iframe class="embed-responsive-item" src="'
            +url+(isYoutube ? '?autoplay=1&iv_load_policy=3&modestbranding=1&rel=0' : '')+'" height="'+height+'px" width="100%" wmode="opaque" allowfullscreen="true"></iframe>');

            modal.modal('show');
        } else if (type == 'video' || ( ! type && vars.trailersPlayer == 'custom')) {
            //set up either to play from youtube or mp4 file
            body.html('<video id="trailer" class="video-js vjs-default-skin vjs-big-play-centered" controls preload="auto" width="100%" height="'+height+'px"> </video>');

            if (url.indexOf('youtube') != -1) {
                videojs('trailer', { "techOrder": ["youtube"]}).src(url).play();
            }
            else {
                videojs('trailer', { "techOrder": ["html5", "flash"]}).src(url).play();
            }

            modal.modal('show');
        } else {
            window.open(url);
        }
    };

    app.utils.getFavicon = function(url) {
        var domain;

        //find & remove protocol (http, ftp, etc.) and get domain
        if (url.indexOf("//") > -1) {
            domain = url.split('/')[2];
        }
        else {
            domain = url.split('/')[0];
        }

        //find & remove port number
        domain = domain.split(':')[0];

        return 'http://www.google.com/s2/favicons?domain='+domain;
    };

    /**
     * Send request to server to report a broken link.
     * 
     * @param  {int|string} id
     * @return void
     */
    app.viewModels.titles.show.report = function(id) {
        app.utils.ajax({
            url: vars.urls.baseUrl+'/links/report',
            data: ko.toJSON({ _token: vars.token, link_id: id }),

            success: function(data) {
                app.utils.noty(data, 'success');
            },
            error: function(data) {
                app.utils.noty(data.responseJSON, 'error');
            }
        });
    };

    /**
     * Rate a link positively or negatively.
     *
     * @param {int|string} id
     * @param {int} rating  0 or 1
     *
     * @return void
     */
    app.viewModels.titles.show.rateLink = function(id, rating) {
        var votes = localStorage.getItem('link_votes');

        if (votes) {
            votes = JSON.parse(votes);
        } else {
            votes = {};
        }
        
        var className     = rating === 'positive' ? '.vote-positive' : '.vote-negative',
            node          = $('[data-id="'+id+'"').find(className+' .votes'),
            currentRating = parseInt(node.html()),
            method        = 'increment';

        //if user already voted on this link decrement his vote
        if (votes[id] && currentRating > 0) {
            node.text(currentRating-1);
            delete votes[id];
            localStorage.setItem('link_votes', JSON.stringify(votes));
            method = 'decrement';
        } else {
            node.text(currentRating+1);
            votes[id] = rating;
            localStorage.setItem('link_votes', JSON.stringify(votes));
            method = 'increment';
        }

        app.utils.ajax({
            url: vars.urls.baseUrl+'/links/rate',
            data: ko.toJSON({ _token: vars.token, link_id: id, rating: rating, method: method }),
            success: function() {
              
            }
        });
    };

})(jQuery);
