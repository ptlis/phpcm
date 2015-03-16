(function() {
    'use strict';


    angular.module('coverageMonitor').service(
        'coverageMonitor.revisions',
        [
            '$http',
            'config.coverageUrl',
            function($http, coverageUrl) {
                return new Revisions($http, coverageUrl);
            }
        ]
    );

    /**
     * Object to retrieve revisions with.
     *
     * @param $http
     * @param coverageUrl
     * @constructor
     */
    function Revisions($http, coverageUrl) {

        this.getList = function getList(successCallback, failureCallback) {

            $http({
                method: 'get',
                url: coverageUrl + 'revision_list.json'
            })
            .success(function(list) {

                // sort commits with most recent ones first.
                list.sort(function(a, b) {
                    var dateA = moment(a.created);
                    var dateB = moment(b.created);

                    if (dateA.isBefore(dateB)) {
                        return 1;

                    } else if (dateB.isBefore(dateA)) {
                        return -1;

                    } else {
                        return 0;
                    }
                });

                successCallback(list);
            })
            .error(failureCallback);
        };

        this.getById = function getById(id, successCallback, failureCallback) {
            $http({
                method: 'get',
                url: coverageUrl + id + '.json'
            })
            .success(successCallback)
            .error(failureCallback);
        };
    }
})();
