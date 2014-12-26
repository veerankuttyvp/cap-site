'use strict';

angular.module('cap', [
  'ngAnimate',
  'ngCookies',
  'cap.controllers',
  'cap.controllers.login',
  'cap.filters',
  'cap.router',
  'cap.services',
  'cap.directives'
])
.config(function($interpolateProvider) {
    $interpolateProvider.startSymbol('[[');
    $interpolateProvider.endSymbol(']]');
})
.config([function() {

  }
]).run(['$rootScope', function($rootScope) {

  }
]);
