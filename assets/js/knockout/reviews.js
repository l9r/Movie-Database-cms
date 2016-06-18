(function($) {
    'use strict'

    app.viewModels.reviews = {

        /**
         * All reviews.
         * 
         * @type ko.observable(Array)
         */
        sourceItems: ko.observableArray([]),

        /**
         * Sorting type and order.
         * 
         * @type ko.observable(String)
         */
        currentSort: ko.observable(),

        /**
         * Whether to who user, critic or all reviews.
         * 
         * @type ko.observable(String)
         */
        currentType: ko.observable(),

        delete: function(review) {
           var self = app.viewModels.reviews;

            app.utils.ajax({
                url: vars.urls.baseUrl+'/movies/'+vars.titleId+'/reviews/'+review.id,
                type: 'DELETE',
                data: ko.toJSON(vars.token),
                success: function() {
                    self.sourceItems.remove(review);
                }
            })
        },

        /**
         * Send request to server to create a new
         * user review.
         * 
         * @return void
         */
        create: function(form) {
            var self = this;

            var params = {
                data: ko.toJSON(app.models.userReview),
                success: function(response) {
                    var exists = false;

                    $.each(self.sourceItems(), function(i,v) {

                        //if user has already written a review for this game we'll just replace
                        //it with this one as that's what backend is doing as well
                        if (v.type == 'user' && v.user_id == parseInt(vars.userId)) {

                            self.sourceItems()[i] = ko.toJS(app.models.userReview);
                            self.sourceItems.notifySubscribers();
                            exists = true;
                            return false;
                        }
                    });

                    if ( ! exists) {
                        self.sourceItems.unshift(ko.toJS(app.models.userReview));
                    }
                    
                    $('#review-modal').modal('hide');
                    app.utils.noty(response, 'success');
                },
                error: function(jq) {
                    $('.alert').remove();
                    app.utils.appendError(jq);
                },
                url: form.action,
            };

            app.utils.ajax(params);
        },
    };

    /**
     * Filters critic reviews on platform dropdown change,
     * if no reviews found fires an ajax request to query
     * review data provider.
     * 
     * @return array
     */
    app.viewModels.reviews.filteredReviews = ko.computed(function() {
        var self = this, filtered;

        //filter by user or critic reviews if user select either
        if (self.currentType() === 'all') {
            filtered = self.sourceItems();
        } else {
            filtered = ko.utils.arrayFilter(self.sourceItems(), function(rev) {
                return rev.type === self.currentType();
            });
        }

        //split current sort by camelCase into type and order params
        var sort = self.currentSort().match(/([A-Z]?[^A-Z]*)/g).slice(0,-1);

        if (sort.length === 2) {
            filtered.sort(app.utils.sort[sort[0]](sort[1]));
        }
        
        return filtered ? filtered : [];

    }, app.viewModels.reviews, {deferEvaluation: true});

    /**
     * New review form model.
     * 
     * @type Object
     */
    app.models.userReview = {
        author: app.display_name,
        body: ko.observable(),
        score: ko.observable(),
        type: 'user',
        _token: vars.token,
        source: vars.trans.siteName,
        user_id: false,
    };

})(jQuery);