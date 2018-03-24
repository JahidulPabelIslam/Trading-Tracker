var app = angular.module("TradingTrackerApp", []);

app.controller("ctrl", function ($scope, $filter) {
    $scope.getTrades = function() {
        var trades = JSON.parse(localStorage.getItem('tradingtrackertrades'));

        if (trades == null) {
            trades = []
        }

        return trades;
    };

    $scope.saveTrades = function() {
        localStorage.setItem('tradingtrackertrades', JSON.stringify($scope.trades));
    };

    $scope.deleteTrade = function(index) {
        $scope.trades.splice(index, 1);
        $scope.saveTrades();
    };

    $scope.addTrade = function() {
        var entryprice = parseFloat($scope.selectedTrade.entryprice, 10);
        var exitprice = parseFloat($scope.selectedTrade.exitprice, 10);

        var pips = 0;
        if ($scope.selectedTrade.type === "Buy") {
            pips = new Decimal(exitprice).minus(entryprice);
        } else {
            pips = new Decimal(entryprice).minus(exitprice);
        }

        pips = parseFloat(pips, 10);

        var name = $scope.selectedTrade.name.toLowerCase();

        if (name.includes("jpy") || name.includes("xau")) {
            $scope.selectedTrade.pips = pips / 0.01;
        } else {
            $scope.selectedTrade.pips = pips / 0.0001;
        }

        $scope.trades.push($scope.selectedTrade);
        $("#addTrade").modal("hide");
        $scope.selectedTrade = [];
        $scope.saveTrades();
    };

    $scope.selectedTrade = {};
    $scope.types = ["Sell", "Buy"];

    $scope.trades = $scope.getTrades();
});