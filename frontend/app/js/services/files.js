(function() {
    'use strict';

    angular.module('coverageMonitor').service(
        'coverageMonitor.files',
        [
            '$http',
            'config.coverageUrl',
            function($http, coverageUrl) {
                return new Files($http, coverageUrl);
            }
        ]
    );

    /**
     * Object to retrieve files with.
     *
     * @param $http
     * @param coverageUrl
     * @constructor
     */
    function Files($http, coverageUrl) {

        this.getByIdName = function getByIdName(id, filename, successCallback, failureCallback) {
            $http({
                method: 'get',
                url: coverageUrl + id + '.json'
            })
            .success(function(revision) {

                var file = null;
                for (var i = 0; i < revision.files.length; i++) {
                    if (filename === revision.files[i].new_filename) {
                        file = revision.files[i];
                    }
                }

                successCallback(file, revision);
            })
            .error(failureCallback);
        };

        /**
         * Get all revisions that the specified file is present in.
         */
        this.getAllRevisionsByName = function getByName(name, successCallback, failureCallback) {
            $http({
                method: 'get',
                url: coverageUrl + 'files_in_revisions.json'
            })
            .success(function(data) {
                if (name in data && data.hasOwnProperty(name)) {
                    successCallback(data[name]);
                } else {
                    successCallback([]);
                }
            })
            .error(failureCallback);
        };
    }
})();
