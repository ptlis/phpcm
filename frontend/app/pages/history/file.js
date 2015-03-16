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
                    'history_file', {
                        url: '/history/file/{new_filename}',
                        templateUrl: 'pages/history/file.html',
                        controller: 'HistoryFileCtrl'
                    }
                );
            }
        ]
    );


    /**
     * Controller.
     */
    angular.module('coverageMonitor').controller(
        'HistoryFileCtrl',
        [
            '$scope',
            '$state',
            '$stateParams',
            'coverageMonitor.files',
            function ($scope, $state, $stateParams, files) {

                $scope.display = {
                    changedOnly: true
                };
                $scope.filename = $stateParams.new_filename;
                $scope.fileInRevisions = [];

                $scope.filterByOperation = function filterByOperation(value) {

                    // Filter to return only changed files
                    if ($scope.display.changedOnly && 'unchanged' != value.operation) {
                        return value;

                    // Unfiltered
                    } else if (!$scope.display.changedOnly) {
                        return value;
                    }

                };

                files.getAllRevisionsByName(
                    $stateParams.new_filename,
                    function(revisionList) {
                        $scope.fileInRevisions = revisionList;
                    }
                );
            }
        ]
    );
})();



