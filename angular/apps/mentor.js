'use strict';

var app = angular.module('cap', [
  'ngAnimate',
  'ngCookies',
  'cap.controllers',
  'cap.controllers.mentor',
  'cap.filters',
  'cap.router',
  'cap.services',
  'cap.directives'
]);

app.config(function($interpolateProvider) {
    $interpolateProvider.startSymbol('[[');
    $interpolateProvider.endSymbol(']]');
});

app.config([function() {

}]);
