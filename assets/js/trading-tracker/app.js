var app = angular.module("TradingTrackerApp", []);
app.controller("ctrl", function ($scope) {
    $scope.trades = [
        {
            "name": "EUR/USD",
            "date": "18/02/2018",
            "lot": 0.01,
            "type": "Buy",
            "startprice": 0.1,
            "endprice": 0.1,
            "pips": 0
        }
    ];
});