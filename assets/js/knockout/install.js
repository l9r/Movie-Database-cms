(function($) {
    'use strict'

    app.viewModels.install = {

        /**
         * Indicate whether or not we're currently performing an ajax request.
         * 
         * @type boolean
         */
        working: ko.observable(false),

        /**
         * Compatability check results.
         * 
         * @type mixed
         */
        compatResults: ko.observable(false),

        /**
         * Whether or not user is allowed to move to next
         * step of installation.
         * 
         * @type boolean
         */
        nextBtnEnabled: ko.observable(false),

        /**
         * On what step of installation we are currently on.
         * 
         * @type String
         */
        currentStep: ko.observable('compat'),

        /**
         * Available installation steps.
         * 
         * @type {Array}
         */
        availableSteps: ['compat', 'db', 'config', 'finalize'],

        /**
         * Steps user has already completed.
         * 
         * @type array
         */
        completedSteps: ko.observableArray(),
       
        /**
         * Send request to server to check from any problems
         * with the server.
         * 
         * @return void
         */
        checkCompat: function() {
            var self = this;
            
            self.working(true);

            app.utils.ajax({
                url: vars.urls.baseUrl + '/install/check-compat',
                data: ko.toJSON({ _token: vars.token }),
                success: function(resp) {

                    if (resp.extensions) {
                        self.compatResults(resp);

                        //if there were no problem allow user
                        //to move to next step
                        if ( ! resp.problem) {
                            self.nextBtnEnabled(true);
                        } else {
                            $('#problem').removeClass('hidden');
                        }

                    } else {
                        app.utils.noty(vars.trans.error, 'error');
                    }

                    self.working(false);
                    self.completedSteps.push('compat');
                },
                error: function(resp) {
                    app.utils.noty(vars.trans.error, 'error');
                    self.working(false);
                }
            })
        },

        /**
         * Send request to server to store admin account and
         * basic site info.
         * 
         * @param  DOM form
         * @return void
         */
        storeBasics: function(form) {
            var self = this;
            
            app.utils.disablePage();

            app.utils.ajax({
                url: vars.urls.baseUrl + '/install/store-basics',
                data: $(form).getFormData(),
                success: function(resp, status, jq) {

                    if (jq.status === 201) {

                        $('.alert').remove();
                        app.utils.enablePage();
                        window.location.hash = 'finalize';
                        self.completedSteps.push('config');
                    }     
                },
                error: function(resp) {

                    $('.alert').remove();

                    if (typeof resp.responseJSON == 'string' || resp.responseJSON instanceof String) {
                        app.utils.appendError(resp.responseJSON, form, 'prepend');
                    } else {
                        app.utils.appendError(resp);
                    }

                    app.utils.enablePage();
                },

            });
        },

        /**
         * Send request to server to create db schema.
         * 
         * @param  DOM form
         * @return void
         */
        prepareDb: function(form) {
            var self = this;

            app.utils.disablePage();

            app.utils.ajax({
                url: vars.urls.baseUrl + '/install/prepare-db',
                data: $(form).getFormData(),
                success: function(resp, status, jq) {

                    if (jq.status === 201) {
                        $('#error').addClass('hidden');
                        app.utils.enablePage();
                        window.location.hash = 'config';
                        self.completedSteps.push('db');
                    }  
                },
                error: function(response) {
                    app.utils.enablePage();

                    if (response.responseJSON) {
                        $('#error #msg').html(response.responseJSON);
                        $('#error').removeClass('hidden');
                    }          
                }
            });
        },

        /**
         * Finalize the installation.
         * 
         * @param  DOM form
         * @return void
         */
        finalize: function(form) {
            var self = this;

            app.utils.disablePage();

            app.utils.ajax({
                url: vars.urls.baseUrl + '/install/finalize',
                data: $(form).getFormData(),
                success: function(resp, status, jq) {

                    if (jq.status === 201) {
                        $('#error').addClass('hidden');
                        app.utils.enablePage();
                        self.completedSteps.push('finalize');

                        //if everything is ok we are done and can redirect
                        //the user to homepage
                        window.location = vars.urls.baseUrl;
                    }  
                },
                error: function(response) {
                    app.utils.enablePage();

                    if (response.responseJSON) {
                        $('#error #msg').html(response.responseJSON);
                        $('#error').removeClass('hidden');
                    }       
                }
            });
        },

        /**
         * Move the installation proccess to next step.
         * 
         * @return void
         */
        nextStep: function() {
            var self = this;

            $.each(self.availableSteps, function(k,v) {

                if (self.currentStep() == v) {
                    
                    //increment current step by one
                    self.currentStep(self.availableSteps[k+1]);

                    window.location.hash = self.availableSteps[k+1];

                    return false;
                }
            });
        },

        /**
         * Initiate the viewModel.
         * 
         * @return void
         */
        start: function() {
            var self = this;

            //handle hash on page load
            self.handleHashChange();

            //and on hash change after page load
            $(window).on('hashchange', function() {
                self.handleHashChange();
            });

            //dont allow user to move to another step manually if he hasn't 
            //completed it yet
            $('.wizard li').click(function(e) {
                var step = $(e.target).closest('a').prop('hash').replace('#', '');

                if ( ! app.utils.inArray(self.completedSteps(), step)) {
                    e.preventDefault();
                }
                
            });

            ko.applyBindings(app.viewModels.install, $("#install")[0]);
        },

        /**
         * Change step and active classes on hash change.
         * 
         * @return void
         */
        handleHashChange: function() {
            var hash = window.location.hash.replace('#', '');

            if (hash) {
                app.viewModels.install.currentStep(hash);

                $('.active').removeClass('active');
                $('.'+hash).addClass('active');
            }      
        }
    };

    /**
     * Enable/disable next step button.
     * 
     * @return Bolean
     */
    app.viewModels.install.enableNext = ko.computed(function() {
        return this.nextBtnEnabled();
    }, app.viewModels.install, {deferEvaluation: true});

})(jQuery);