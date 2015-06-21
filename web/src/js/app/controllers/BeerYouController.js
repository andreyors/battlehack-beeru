
(function(define) {

    'use strict';

    define([], function() {

        var BeerYouController = function($scope, styles, categories, beers) {

            $scope.loadStyles = function() {
                styles.all().then(function(data) {
                    $scope.styles = data.data;
                })
            };

            $scope.changeStyle = function() {
                $scope.beers = filterList();
            };

            $scope.loadCategories = function() {
                categories.all().then(function(data) {
                    $scope.categories = data.data;
                })
            };

            function filterList() {

                beers.all().then(function(data) {
                    if (!$scope.style) {
                        $scope.beers = data;
                        return;
                    }

                    var style = $scope.style.id;

                    var result = [], i;

                    for (i = 0; i < data.length; i++) {
                        if (data[i].style_id == style) {
                            result.push(data[i]);
                        }
                    }

                    $scope.beers = result;
                });

            };

            filterList();
        };

        return ['$scope', 'styles', 'categories', 'beers', BeerYouController];

    });

}(define));