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
                    'revision_file', {
                        url: '/revision/{identifier}/{new_filename}',
                        templateUrl: 'pages/revision_file/file.html',
                        controller: 'RevisionFileCtrl'
                    }
                );
            }
        ]
    );


    /**
     * Controller.
     */
    angular.module('coverageMonitor').controller(
        'RevisionFileCtrl',
        [
            '$scope',
            '$state',
            '$stateParams',
            'coverageMonitor.files',
            function ($scope, $state, $stateParams, files) {

                $scope.file = {};
                $scope.revision = {};
                $scope.fileInRevisions = [];

                $scope.otherRevision = {
                    selected: null
                };

                /**
                 * On selected revision change redirect to that version of the file.
                 */
                $scope.revisionChanged = function revisionChanged() {
                    $state.go(
                        'revision_file',
                        {
                            identifier: $scope.otherRevision.selected.identifier,
                            new_filename: $stateParams.new_filename
                        }
                    );
                };

                files.getByIdName(
                    $stateParams.identifier,
                    $stateParams.new_filename,
                    function(file, revision) {
                        $scope.file = file;
                        $scope.revision = revision;
                    }
                );

                files.getAllRevisionsByName(
                    $stateParams.new_filename,
                    function(revisionList) {
                        $scope.fileInRevisions = revisionList;

                        // Set currently selected revision
                        for (var i = 0; i < revisionList.length; i++) {
                            if ($stateParams.identifier === revisionList[i].identifier) {
                                $scope.otherRevision.selected = revisionList[i];
                            }
                        }
                    }
                );
            }
        ]
    );
})();



