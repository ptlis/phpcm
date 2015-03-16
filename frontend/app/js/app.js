(function() {
    'use strict';

    /**
     * Main application module.
     */
    angular.module(
        'coverageMonitor',
        [
            'ui.router',
            'angularMoment'
        ]
    );


    /**
     * Define the default route.
     */
    angular.module('coverageMonitor').config(
        [
            '$urlRouterProvider',
            function ($urlRouterProvider) {
                $urlRouterProvider.otherwise('/');
            }
        ]
    );

})();
