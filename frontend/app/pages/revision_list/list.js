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
                    'home', {
                        url: '/',
                        templateUrl: 'pages/revision_list/list.html',
                        controller: 'ListCtrl'
                    }
                );
            }
        ]
    );


    /**
     * Controller.
     */
    angular.module('coverageMonitor').controller(
        'ListCtrl',
        [
            '$scope',
            'coverageMonitor.revisions',
            function ($scope, revisions) {

                $scope.revisionList = [];

                revisions.getList(
                    function(revisionList) {
                        $scope.revisionList = revisionList;
                    }
                );
            }
        ]
    );
})();



