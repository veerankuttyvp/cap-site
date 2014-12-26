'use strict';

var app = angular.module('cap', [
  'ngAnimate',
  'ngCookies',
  'cap.controllers',
  'cap.controllers.questionnaire',
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
