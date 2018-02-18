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

        var entryprice = parseFloat($scope.newtrade.entryprice, 10);
        var exitprice = parseFloat($scope.newtrade.exitprice, 10);

        var pips = 0;
        if ($scope.newtrade.type === "Buy") {
            pips = new Decimal(exitprice).minus(entryprice);
        } else {
            pips = new Decimal(entryprice).minus(exitprice);
        }

        pips = parseFloat(pips, 10);

        var name = $scope.newtrade.name.toLowerCase();

        if (name.includes("jpy") || name.includes("xau")) {
            $scope.newtrade.pips = pips / 0.01;
        } else {
            $scope.newtrade.pips = pips / 0.0001;
        }

        $scope.trades.push($scope.newtrade);
        $("#addTrade").modal("hide");
        $scope.newtrade = [];
    };
});