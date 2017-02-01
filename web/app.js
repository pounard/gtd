/* eslint no-alert: 0 */
"use strict";

if (!basePath) {
  var basePath = '/';
}

var app = angular.module('GTD', [
  'ngRoute',
  'mobile-angular-ui',
  // touch/drag feature: this is from 'mobile-angular-ui.gestures.js'.
  'mobile-angular-ui.gestures'
]);

app.run(function($transform) {
  window.$transform = $transform;
});

//
// You can configure ngRoute as always, but to take advantage of SharedState location
// feature (i.e. close sidebar on backbutton) you should setup 'reloadOnSearch: false'
// in order to avoid unwanted routing.
//
app.config(function($routeProvider) {
  $routeProvider.when('/', {templateUrl: basePath + 'app/tasks.html', reloadOnSearch: true});
  $routeProvider.when('/task/:taskId', {templateUrl: basePath + 'app/task.html', reloadOnSearch: true});
//  $routeProvider.when('/toggle', {templateUrl: 'toggle.html', reloadOnSearch: false});
//  $routeProvider.when('/tabs', {templateUrl: 'tabs.html', reloadOnSearch: false});
//  $routeProvider.when('/accordion', {templateUrl: 'accordion.html', reloadOnSearch: false});
//  $routeProvider.when('/overlay', {templateUrl: 'overlay.html', reloadOnSearch: false});
//  $routeProvider.when('/forms', {templateUrl: 'forms.html', reloadOnSearch: false});
//  $routeProvider.when('/dropdown', {templateUrl: 'dropdown.html', reloadOnSearch: false});
//  $routeProvider.when('/touch', {templateUrl: 'touch.html', reloadOnSearch: false});
//  $routeProvider.when('/swipe', {templateUrl: 'swipe.html', reloadOnSearch: false});
//  $routeProvider.when('/drag', {templateUrl: 'drag.html', reloadOnSearch: false});
//  $routeProvider.when('/drag2', {templateUrl: 'drag2.html', reloadOnSearch: false});
//  $routeProvider.when('/carousel', {templateUrl: 'carousel.html', reloadOnSearch: false});
});

// Task list page controller
app.controller('TaskListController', function($rootScope, $scope, $http) {
  $http.get('rest/task/tasks').then(function(response) {
    $scope.tasks = response.data.tasks;
  });
});

// Single task controller
//Task list page controller
app.controller('TaskController', function($rootScope, $scope, $http, $routeParams) {
  $http.get('rest/task/' + $routeParams.taskId).then(function(response) {
    $scope.task = response.data;
  });
});

// Main controller, for main UI only
app.controller('MainController', function($rootScope, $scope) {
});
