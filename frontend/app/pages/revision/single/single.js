(function() {
    'use strict';

    /**
     * Route.
     */
    angular.module('coverageMonitor').config(
        [
            '$stateProvider',
            function ($stateProvider) {
                $stateProvider.state(
                    'revision_single', {
                        url: '/revision/{identifier}',
                        templateUrl: 'pages/revision/single/single.html',
                        controller: 'SingleRevisionCtrl'
                    }
                );
            }
        ]
    );


    /**
     * Controller.
     */
    angular.module('coverageMonitor').controller(
        'SingleRevisionCtrl',
        [
            '$scope',
            '$stateParams',
            'coverageMonitor.revisions',
            function ($scope, $stateParams, revisions) {

                $scope.revision = {};

                revisions.getById(
                    $stateParams.identifier,
                    function(revision) {
                        $scope.revision = revision;
                    }
                );
            }
        ]
    );
})();



