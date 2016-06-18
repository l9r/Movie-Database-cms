(function() {
	'use strict';

	window.app = {
		viewModels: {
			titles: {}
		},
		perm: ko.observable(false),
		models: {},
		utils: {}
	};

	/**
	 * Load disqus comments.
	 *
	 * @return void
	 */
	app.loadDisqus = function() {
		setTimeout(function() {
		   	var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
		    dsq.src = '//' + vars.disqus + '.disqus.com/embed.js';
		    (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
		}, 1000);

	};

	/**
	 * start gallery on img click.
	 *
	 * @return void
	 */
	app.startGallery = function() {
		$('#images-col').on('click', 'img', function() {
            vars.imgs = $('#images-col img').map(function() {
            	return this.src.replace(/\/w[0-9]+\//, '/original/').replace('.thumb', '');
            }).get();

            blueimp.Gallery(vars.imgs);
        });
	};

	String.prototype.trunc = String.prototype.trunc ||
    	function(n) {
        	return this.length > n ? this.substr(0,n-1)+'...' : this;
      	};
})();

