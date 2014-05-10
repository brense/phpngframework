app.controller('UserController', ['$scope', '$http',
    function ($scope, $http) {
        $http.get('api/user', { cache: false }).success(function (response) {
            $scope.users = response;
            console.log($scope.users);
        });
    }
]);