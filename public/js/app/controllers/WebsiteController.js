app.controller('WebsiteController', ['$scope', '$http',
    function($scope, $http){
        $scope.lang = '';
        $scope.activeLanguages = [];
        $scope.languages = [];
	$scope.content = {};
        $scope.languagesByIso = {};
        $scope.newSection = resetScope();
        
        // construtor
        loadContent();
        
        function loadContent(){
            $http.get('content', {cache: false}).success(function(response){
                $scope.content = response;
                var langs = [];
                for(var lang in response.title)
                    langs.push(lang);
                $scope.lang = langs[0];
                $http.get('languages', {cache: false}).success(function(response){
                    $scope.languages = response;
                    for(var i = 0; i < response.length; i++){
                        $scope.languagesByIso[response[i].iso] = response[i];
                        if($.inArray(response[i].iso, langs) >= 0)
                            $scope.activeLanguages.push(response[i]);
                    }
                    if (!$scope.$$phase)
                        $scope.$apply();
                });
            });
        };
        
        $scope.setLanguage = function(lang){
            $scope.lang = lang;
        };
        
        $scope.addLanguage = function(){
            $scope.activeLanguages.push($scope.languagesByIso[$scope.newLanguage]);
            $scope.lang = $scope.newLanguage;
        };
        
        $scope.removeLanguage = function(lang){
            var index = 0;
            var reset = [];
            for(var i=0; i<$scope.activeLanguages.length; i++){
                if($scope.activeLanguages[i].iso !== lang){
                    reset[index] = $scope.activeLanguages[i];
                    index++;
                } else {
                    $http.delete('content/language?lang=' + lang, {cache: false}).success(function(response){
                        $scope.content = response;
                        if (!$scope.$$phase)
                            $scope.$apply();
                    });
                }
            }
            $scope.activeLanguages = reset;
            $scope.lang = reset[0].iso;
        };
        
        $scope.save = function(){
            $http.put('content/' + $scope.content.id, $scope.content, {cache: false}).success(function(response){});
        };
        
        $scope.addSection = function(){
            if($scope.newSection.parent != undefined && $scope.newSection.parent.length > 0){
                delete $scope.newSection['sub_sections'];
                for(var key in $scope.content.sections){
                    if($scope.content.sections[key].name == $scope.newSection.parent){
                        $scope.newSection.order = $scope.content.sections[key].sub_sections.length
                        $scope.content.sections[key].sub_sections.push($scope.newSection);
                    }
                }
            } else {
                $scope.newSection.order = $scope.content.sections.length;
                $scope.content.sections.push($scope.newSection);
            }
            $scope.save();
            $scope.newSection = resetScope();
            $scope.addSectionForm.$setPristine();
        };
        
        $scope.sectionsOptions = {
            accept: function(data) {
                return (data.sub_sections != undefined);
            },
            orderChanged: function(scope, sourceItem, sourceIndex, destIndex) {
                $scope.$apply();
            },
            itemMoved: function(sourceScope, sourceItem, sourceIndex, destScope, destIndex) {
                $scope.$apply();
                $scope.save();
            }
	};
        
	$scope.subsectionsOptions = {
            accept: function(data) {
                return (data.sub_sections == undefined);
            },
            orderChanged: function(scope, sourceItem, sourceIndex, destIndex) {
                $scope.$apply();
            },
            itemMoved: function(sourceScope, sourceItem, sourceIndex, destScope, destIndex) {
                $scope.$apply();
                $scope.save();
            }
	};
        
        function resetScope(){
            return {
                'menu':{},
                'sub_sections':[],
                'order':1
            };
        };
    }
]);