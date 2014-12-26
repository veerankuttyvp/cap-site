'use strict';

/* Directives */

angular.module('cap.directives', []).

directive('ngEnter', [
  function() {
    return {
      restrict:"EAC",
      link: function(scope, element, attrs) {
        element.bind("keypress", function(event) {
          if(event.which === 13) {
            scope.$apply(function(){
              scope.$eval(attrs.ngEnter);
            });
            event.preventDefault();
          }
        });
      }
    };
  }
]).

directive('saqList', [
  function() {
    return {
      restrict: 'E',
      templateUrl: '/partials/saq-list',
      link: function(scope, element, attrs) {

      }
    };
  }
]).

directive('mentorsList', [
  function() {
    return {
      restrict: 'E',
      templateUrl: '/partials/mentors-list',
      link: function(scope, element, attrs) {

      }
    };
  }
]).

directive('menteesList', [
  function() {
    return {
      restrict: 'E',
      templateUrl: '/partials/mentees-list',
      link: function(scope, element, attrs) {

      }
    };
  }
]).

directive("fileread", [
  function () {
    return {
      scope: {
        fileread: "="
      },
      link: function (scope, element, attributes) {
        element.bind("change", function (changeEvent) {
          scope.$apply(function () {
            scope.fileread = changeEvent.target.files[0];
            // or all selected files:
            // scope.fileread = changeEvent.target.files;
          });
        });
      }
    }
  }
]);
