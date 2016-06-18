app.viewModels.autocomplete = {

    /**
     * Holds search bar autocomplete query.
     * 
     * @type ko.observable(String),
     */
    query: ko.observable(),

    /**
     * Holds autocomplete query results.
     * 
     * @type ko.observable(Array)
     */
    autocompleteResults: ko.observableArray()
};

/**
 * Send autocomplete request to server on query value change.
 *
 * @return void
 */
app.viewModels.autocomplete.fetch = ko.computed(function() {
    var self  = this,
        query = self.query();

        //bail if query is falsy
        if ( ! query) return;

        app.utils.ajax({
            type: 'GET',
            url: vars.urls.baseUrl + '/typeahead/' + query,
            success: function(data) {
                self.autocompleteResults(data);
            }
        });

}, app.viewModels.autocomplete).extend({ rateLimit: { method: "notifyWhenChangesStop", timeout: 400 } });

/**
 * Show autcomplete container when we have some results.
 * 
 * @return ko.computed
 */
app.viewModels.autocomplete.showAutocomplete = ko.computed(function() {
    var self  = this;
   
    if (self.autocompleteResults().length > 0) {
        $('.autocomplete-container').fadeIn();
    }
}, app.viewModels.autocomplete);