'use strict';

/* Services */

// Demonstrate how to register services
// In this case it is a simple value service.
angular.module('cap.services', []).
factory('customer', ['$http',
    function($http) {
        var self = this;
        self.customer = null;

        return {
            get: function(cb) {
                console.log('get customer');
                if (self.customer) {
                    return self.customer;
                }
                $http.get('/rest/customer/current', {
                    headers: {
                        'Content-Type': 'application/json'
                    }
                }).success(function(data, status) {
                    self.customer = data;
                    cb(null, self.customer);
                }).error(function(data, status){
                    cb(status,data)
                });

            },
        }
    }
]).

factory('share', function() {
    return {
        messages : {
            show : false,
            type : '',
            message : ''
        },
        loader : {
            show : false
        }
    };
});
