(function($) {
	'use strict'

	app.viewModels.media = {

		/**
		 * Holds all media items.
		 * 
		 * @type ko.observable(Array)
		 */
		sourceItems: ko.observableArray(),

		/**
		 * Parameters to filter and order media items on.
		 * 
		 * @type {Object}
		 */
		params: {
			type: ko.observable(),
			order: ko.observable(),
			query: ko.observable(),
		},

		/**
		 * Whether or not CKE editor exists on the current page.
		 * 
		 * @type boolean
		 */
		ckeExists: ko.observable(false),

		/**
		 * Whether or not we are currently editing or creating a new title.
		 * 
		 * @type boolean
		 */
		editingTitle: ko.observable(false),

		/**
		 * Whether or not we are currently editing the slider.
		 * 
		 * @type boolean
		 */
		editingSlider: ko.observable(false),

		/**
		 * Wheter or not we should keep original name when uploading a file.
		 * 
		 * @type boolean
		 */
		keepOriginalName: ko.observable(false),

		/**
		 * Delete given media item from server and sourceItems.
		 * 
		 * @param  Object item
		 * @return void
		 */
		deleteItem: function(item) {
			app.utils.ajax({
				url: vars.urls.baseUrl + '/media/' + item.id,
				type: 'DELETE',
				data: ko.toJSON({ _token: vars.token}),
				success: function(data) {
					app.viewModels.media.sourceItems.remove(item);
					app.utils.noty(data, 'success');
				},
				error: function(data) {
	            	app.utils.enablePage();
	            	app.utils.noty(data.responseJSON, 'error');	
	            }
			});
		},

		/**
		 * Show absolute image path in alert.
		 * 
		 * @param  object item
		 * @return void
		 */
		showPath: function(item) {
			var alert = '<div id="full-path" class="alert alert-success fade in">'+
                        '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'+
                        app.viewModels.media.makePath(item.path)+'</div>';

			$('#full-path').html(alert);
		},

		/**
		 * Make a fully qualified item path.
		 * 
		 * @param  string path
		 * @return string
		 */
		makePath: function(path) {

			//if we've got absolute url return it 
			if (path.indexOf('http') >= 0) {
				return path;
			}

			//otherwise make absolute from relative
			return vars.urls.baseUrl + '/' + path;
		},

		/**
		 * Insert given image into CKE editor.
		 * 
		 * @param  Object image
		 * @return void
		 */
		insertIntoCKE: function(image) {
			var self  = app.viewModels.media,
				modal = $('#upload-media-modal');

			//if cke editor exists on the page we'll insert image when it is clicked
			if (self.ckeExists()) {
				CKEDITOR.instances['body'].insertHtml(
					'<img src="'+self.makePath(image.path)+'" class="img-responsive">');

			//otherwise if we're currently editing or creating a game on click
			//we will attach clicked image to that game
			} else if (self.editingTitle()) {
				app.models.title.images.push(image);
				
			} else if (self.editingSlider()) {
				app.models.slide.image(image.path);

			}

			modal.modal('hide');
		},

		/**
		 * Uri to hit for paginated results.
		 * 
		 * @type {String}
		 */
		uri: 'media',

		/**
		 * Innitiate file uploader.
		 * 
		 * @return void
		 */
		start: function() {
			var uploadButton = $('<button/>').addClass('btn btn-primary')
	        	.prop('disabled', true)
	        	.text('Processing...')
	        	.on('click', function () {
		            var $this = $(this), data = $this.data();

		            $this.off('click').text('Abort').on('click', function () {
		                $this.remove();
		                data.abort();
		            });
		            
		            data.submit().always(function () {
		                $this.remove();
		            });
		        });

	        $('#fileupload').bind('fileuploadsubmit', function (e, data) {
			    data.formData = { useOriginalName: app.viewModels.media.keepOriginalName(), _token: vars.token };
			});

		    $('#fileupload').fileupload({
		        url: vars.urls.baseUrl + '/media',
		        dataType: 'json',
		        autoUpload: false,
		        disableImageResize: /Android(?!.*Chrome)|Opera/.test(window.navigator.userAgent),
		        previewMaxWidth: 100,
		        previewMaxHeight: 100,
		        previewCrop: true
		    }).on('fileuploadadd', function (e, data) {
		        data.context = $('<div/>').appendTo('#files');
		        $.each(data.files, function (index, file) {
		            var node = $('<p/>').append($('<span/>').text(file.name.trunc(25)));
		            if (!index) {
		                node
		                    .append('<br>')
		                    .append(uploadButton.clone(true).data(data));
		            }
		            node.appendTo(data.context);
		        });
		    }).on('fileuploadprocessalways', function (e, data) {

		        var index = data.index,
		            file = data.files[index],
		            node = $(data.context.children()[index]);

		        if (file.preview) {
		            node.prepend('<br>').prepend(file.preview);
		        }
		        if (file.error) {
		            node.append('<br>').append($('<span class="text-danger"/>').text(file.error));
		        }
		        if (index + 1 === data.files.length) {
		            data.context.find('button').text('Upload').prop('disabled', !!data.files.error);
		        }

		    }).on('fileuploadprogressall', function (e, data) {

		        //handle progress bar
		        var progress = parseInt(data.loaded / data.total * 100, 10);
		        $('#progress .progress-bar').css('width', progress + '%').css('visibility', 'visible');

		    }).on('fileuploaddone', function (e, data) {
		        
		        $.each(data.result, function (i, result) {
		          
		            //remove preview image
		            data.context.children()[i].remove();

		            //push the uploaded image into current images array
		            app.viewModels.media.sourceItems.unshift(result);
		        });

		        //reset progress bar
		        $('.progress-bar').css('width', 0);
		        $('.progress-bar').css('visibility', 'hidden');

		    }).on('fileuploadfail', function (e, r) {

		        //reset progress bar and remove previous errors
		        $('.alert').remove();
		        $('.progress-bar').css('width', 0);
		        $('.progress-bar').css('visibility', 'hidden');

		        //append new alert for each error we receive from server
		        $.each(r.jqXHR.responseJSON, function(i,v) {

		            var alert = '<div class="alert alert-danger fade in clearfix"><button type="button"'+
		                        ' class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'+
		                        '<i class="fa fa-exclamation-triangle"></i> ' + v + '</div>'

		            $('#errors').append(alert);
		        });

		    }).prop('disabled', !$.support.fileInput).parent().addClass($.support.fileInput ? undefined : 'disabled');
		},
	};

})(jQuery);