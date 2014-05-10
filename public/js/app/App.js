app.config(function($routeProvider) {
    $routeProvider
        .when('/dashboard', {
            templateUrl: 'tpl/partials/dashboard.html',
            controller: 'DashboardController'
	})
        .when('/website', {
            templateUrl: 'tpl/partials/website.html',
            controller: 'WebsiteController'
	})
        .when('/users', {
            templateUrl: 'tpl/partials/users.html',
            controller: 'UserController'
    })
        .when('/content', {
            templateUrl: 'tpl/partials/content.html',
            controller: 'ContentController'
    })
        .when('/settings', {
            templateUrl: 'tpl/partials/settings.html',
            controller: 'SettingsController'
    })
        .otherwise({redirectTo: '/dashboard'}
    );
});

function MainController($scope, $location) {
    $scope.user = user;
    $scope.getClass = function(path) {
        if ($location.path().substr(0, path.length) == path) {
            return "active"
        } else {
            return ""
        }
    }
}

Object.size = function(obj) {
    var size = 0, key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    return size;
};