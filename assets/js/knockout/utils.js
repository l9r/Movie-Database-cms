(function($) {
	'use strict';

/**
 * Returns all of forms data in json format.
 * 
 * @return JSON
 */
$.fn.getFormData = function() {
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        console.log("------");
        console.log(this);
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });

    return ko.toJSON(o);
};

/**
 * Checks if array contains specified string, case insensitive.
 * 
 * @param  array haystack
 * @param  string needle
 * @return boolean
 */
app.utils.inArray = function(haystack, needle) {
	var match = false;

	$.each(haystack, function(ind, val) { 
		if (match || val.toLowerCase() === needle.toLowerCase()) {
			match = true;
			return false;
		}
	});

	return match;
}

/*
|--------------------------------------------------------------------------
| Wrapper around jquery ajax function
|--------------------------------------------------------------------------
*/
app.utils.ajax = function(params) {
	return $.ajax({
	    url: params.url,
	    type: params.type ? params.type : 'POST',
	    data: params.data,
	    contentType: 'application/json; charset=utf-8',
	    dataType: params.dataType ? params.dataType : 'json',
	    success: params.success ? params.success : function(data) { app.utils.noty(data, 'success') },
	    error: params.error ? params.error : function() { app.utils.noty(vars.trans.error, 'error') },
	    complete: function() { app.utils.hideSpinners(); }
	});
}

/**
 * Return parameters to use for opening pop up window.
 * 
 * @return string
 */
app.utils.getPopupParams = function() {
    return 'location=no,menubar=no,resizable=yes,scrollbars=yes,status=no,titlebar=yes,toolbar=no,channelmode=yes,fullscreen=yes,width=800,height=500';
}

/**
 * Create a notification using noty library.
 * 
 * @param  string/html content
 * @param  string type
 * @return void
 */
app.utils.noty = function(content, type) {
	noty({text: content, type: type, timeout: 2500});
};

/**
 * Show the ajax loading icon after specified
 * element.
 * 
 * @param  string element
 * @return void
 */
app.utils.showSpinner = function(element) {
	if (element) {
		$(element).after('<div class="inline-spinner"><i class="fa fa-spinner fa-spin"></i></div>');
	} else {
		$(':submit').after('<div class="inline-spinner"><i class="fa fa-spinner fa-spin"></i></div>');
	}
};

/**
 * Removes all ajax loading icons from document.
 * 
 * @return void
 */
app.utils.hideSpinners = function() {
	$('.inline-spinner').remove();
};

app.utils.appendError = function(errors, element, position, parent) {

    //if we get passed a string as error just append it to given element
    if (typeof errors == 'string' || errors instanceof String) {
        var err = '<div class="alert alert-danger fade in clearfix"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><i class="fa fa-exclamation-triangle"></i> ' + errors + '</div>';

        if (position === 'append') {
            $(element).append(err);
        } else if (position === 'prepend') {
            $(element).prepend(err);
        }
        else {
            $(element).after(err);
        }

    //otherwise we will append each given error under corresponding input fields
    } else {
        //remove old error messages and coloring
        $('.help-block').remove();
        $('.form-group').removeClass('has-error');
        
        $.each(errors.responseJSON, function(i,v) {

            //if we have parent element we'll make sure to append
            //errors only to that parents descendant fields
            if (parent) {
                var $el = $(parent+' #'+i);
            } else {
                 var $el = $('#'+i);
            }
           
            //show error messages and color input fields
            $el.after('<div class="help-block">'+v+'</div>');
            $el.parent().addClass('has-error');
        });

         //if we have a captcha field on the form we'll refresh the image now
        if (errors.responseJSON.human || errors.responseJSON.captcha) {
            app.utils.refreshCaptcha(parent);
        }
    }
};

/**
 * Refresh captcha image with a new one.
 * 
 * @param  string el
 * @return void
 */
app.utils.refreshCaptcha = function(el) {

    var parent = el ? el : '#footer',
        url    = vars.urls.baseUrl + '/captcha?',
        rand   = 1 + Math.floor(Math.random() * 10000);

    $(parent+' .captcha-image').attr('src', url+rand);
}

/*
|--------------------------------------------------------------------------
| Sorting helpers
|--------------------------------------------------------------------------
*/
app.utils.sort = {};

/**
 * Sort by release date.
 * 
 * @param  string order
 * @return array
 */
app.utils.sort.date = function(order) {
	var order = order.toLowerCase();

    return function(left, right) {
    	var spoof = new Date(1950, 1, 1);
     	var dateA = new Date(right.release_date ? right.release_date : right.created_at);
     	var dateB = new Date(left.release_date ? left.release_date : left.created_at);

	    //if we've failed to turn the timestamp into new date
	    //for some reason we'll use spoof we created earlier so
	    //the sorting doesn't get messed up         
	    var difference = (dateA === 'Invalid Date' ? spoof : dateA) - (dateB === 'Invalid Date' ? spoof : dateB);

	    return ! order || order === 'desc' ? difference : difference * -1;
    }
};

/**
 * Sort by rating.
 * 
 * @param  string order
 * @return array
 */
app.utils.sort.score = function(order) {
    var order = order.toLowerCase();

    return function(left, right) {   
    	return ! order || order === 'desc' ? (left.score - right.score) * -1 : (left.score - right.score);
    }
};

/**
 * Sort alphabetically.
 * 
 * @param  string order
 * @return array
 */
app.utils.sort.alpha = function(order) {
	var order = order.toLowerCase();

	return function(left, right) {
    	return left.title === right.title ? 0 : (left.title < right.title ? -1 : 1);
    };
};

/**
 * Add an overlay over page so user can't interact with it.
 * 
 * @return void
 */
app.utils.disablePage = function() {
	$('#main-loading-outter').show();
};

/**
 * Remove page overlay.
 * 
 * @return void
 */
app.utils.enablePage = function() {
    $('#main-loading-outter').hide();
};

app.utils.getUrlParams = function(name) {
    var query = window.location.search.substring(1),
        vars  = query.split("&");

    for (var i=0;i<vars.length;i++) {
        var pair = vars[i].split("=");

        if (pair[0] == name) { 
            return decodeURIComponent(pair[1]);
        }
    }

    return false;
};

/**
 * Capitalize first string letter.
 * 
 * @param  {[type]} string
 * @return {[type]}       
 */
app.utils.ucFirst = function (string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

})(jQuery);
