var app = angular.module("TradingTrackerApp", []);
app.controller("ctrl", function ($scope, $filter) {
    $scope.newtrade = [];
    $scope.types = ["Sell", "Buy"];

    $scope.trades = [
        {
            "name": "EUR/USD",
            "date": "18/02/2018",
            "lot": 0.01,
            "type": "Buy",
            "entryprice": 0.1,
            "exitprice": 0.1,
            "pips": 0
        }
    ];

    $scope.addTrade = function() {
        $scope.newtrade.date = $filter('date')($scope.newtrade.date, "dd/MM/yyyy");
        $scope.trades.push($scope.newtrade);
        $("#addTrade").modal("hide");
        $scope.newtrade = [];
    };
});