/* Controllers */
angular.module('cap.controllers.login', []).

controller('PasswordCtrl', ['$scope', '$element', '$http', '$timeout', '$window', '$cookies',
    function($scope, $element, $http, $timeout, $window, $cookies) {

        $scope.init = function(token){
            $scope.password  = '';
            $scope.password2 = '';
            $scope.token     = token;
            $scope.success   = false;
            $scope.inProgress = false;
        }

        $scope.submitPassword = function(){
            $scope.inProgress = true;

            console.log($scope.token_value);
            /* validate new and confirm pwords */
            if (
                ($scope.password != '' && $scope.password2 != '') &&
                ($scope.password === $scope.password2)
                ) {

                var params = {
                    'password': $scope.password,
                    'password2': $scope.password2,
                    'token': $scope.token
                }

                $http.post("/password",angular.toJson(params)).success(function(data, status) {
                    if(data.success){
                        $scope.success = true;
                        $scope.msg = "Password changed successfully";
                        document.location.href = "/dashboard";
                    } else {
                        $scope.success = false;
                        $scope.msg = "Unable to set your password.  Please contact your administrator.";
                        $scope.inProgress = false;
                    }
                }).error(function(data, success){
                    $scope.success = false;
                    $scope.msg = "Unable to set your password.  Please contact your administrator.";
                    $scope.inProgress = false;
                });

            } else {
                $scope.inProgress = false;
                $scope.msg = "Please make sure the passwords match.";
            }
        }
    }
]).
controller('LoginCtrl', ['$scope', '$element', '$http', '$timeout', '$window', '$cookies',
    function($scope, $element, $http, $timeout, $window, $cookies) {

        $scope.init = function(){
            $scope.email = '';
            $scope.password = '';
            $scope.success = false;
        }

        /* TODO: do something with this */
        $scope.forgotPassword = function(){
            $scope.is_valid = $scope.validate_form();
            if ($scope.is_valid) {
                params = {
                    'email': $scope.email,
                }
                $http({
                    method: 'post',
                    url: "/user/register",
                    data: angular.toJson(params),
                    headers: {
                        'Content-Type': 'application/json'
                    }
                }).success(function(data, status) {
                    if(data.status == "sent"){
                        $scope.msg = "Your request sent Successfully, Please check your email";
                    } else {
                        $scope.msg = "Some error occured";
                    }
                }).error(function(data, success){
                });
            }
        }

        $scope.submitLogin = function() {
            console.log('submit login');
            if ($scope.loginForm.$pristine) {
            	return;
            }
            if (!$scope.loginForm.$valid) {
                $scope.msg = "Your email or password was incorrect.";
            } else {

                $scope.inProgress = true;
                $scope.success    = true;
                $scope.msg        = "Logging in...";

                var params = {
                    'email': $scope.email,
                    'password': $scope.password,
                }

                $http({
                    method: 'post',
                    url: "/login",
                    data: angular.toJson(params),
                    headers: {
                        'Content-Type': 'application/json'
                    }
                }).success(function(data, status) {
                    if(data.login){
                        $scope.inProgress = false;

                        $timeout(function() {
                            $window.location.href = '/dashboard';
                        });
                        //document.location.href = "./dashboard";
                    } else {
                        $scope.inProgress = false;
                        $scope.success = false;
                        $scope.msg = data.message || "Your email or password was incorrect.";
                    }

                }).error(function(data, success){
                    $scope.success = false;
                    $scope.inProgress = false;
                    $scope.msg = "An internal error ocurred. Please contact your administrator.";
                });
            }
        }

    }
]).
controller('FooCtrl', ['$scope', '$window',
	function($scope, $window) {
	}
]);
