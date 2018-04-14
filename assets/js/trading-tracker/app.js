(function () {
    'use strict';

    var app = angular.module("TradingTrackerApp", []);

    app.controller("ctrl", function ($scope, $filter) {

        $scope.getDateOptions = function() {
            var options = [];

            var trades = $scope.getTrades();

            for (var i = 0; i < trades.length; i++) {
                var trade = trades[i];

                options.push(trade.date);
            }

            return options;
        };

        $scope.setPage = function(page) {
            $scope.page = page;
        };

        $scope.getPages = function() {
            var total = $scope.getFilteredTrades().length;
            var last = Math.ceil(total / $scope.limit);
            var pages = [];

            for (var i = 0; i < last; i++) {
                pages.push(i);
            }

            return pages;
        };

        $scope.dateFilter = function(trade) {
            if ($scope.dateInput == "" || !$scope.dateInput) {
                return true
            } else {
                var inputDate =  new Date($scope.dateInput).setHours(0,0,0,0);
                var elemDate =  new Date(trade.date).setHours(0,0,0,0);

                return inputDate == elemDate;
            }
        };

        $scope.getFilteredTrades = function() {
            var trades = $filter('filter')($scope.trades, $scope.searchfilters);
            trades = $filter('filter')(trades, $scope.dateFilter);
            return trades;
        };

        $scope.getTotalPips = function() {
            var trades = $scope.getFilteredTrades();
            var pips = 0;

            for (var i = 0; i < trades.length; i++) {
                var trade = trades[i];
                pips = new Decimal(pips).add(trade.pips);
            }

            pips = parseFloat(pips);

            return pips;
        };

        $scope.getPipsLeft = function() {
            return parseFloat(new Decimal($scope.pipsTarget).minus($scope.getTotalPips()));
        };

        $scope.getTrades = function() {
            var trades = JSON.parse(localStorage.getItem('tradingtrackertrades'));

            if (trades == null) {
                trades = [];
            }

            return trades;
        };

        $scope.newTrade = function() {
            $scope.selectedTrade = {};
        };

        $scope.saveTrades = function() {
            localStorage.setItem('tradingtrackertrades', JSON.stringify($scope.trades));
            window.tt.stickyFooter.expandSection();
        };

        $scope.deleteTrade = function(trade) {
            var index = $scope.trades.indexOf(trade);
            $scope.trades.splice(index, 1);
            $scope.saveTrades();
        };

        $scope.selectTrade = function(trade) {
            $scope.selectedTrade = trade;
            $scope.selectedTrade.date = new Date($scope.selectedTrade.date);
            var index = $scope.trades.indexOf(trade);
            $scope.selectedTrade.index = index;
            $("#addTrade").modal("show");
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
                $scope.selectedTrade.pips = new Decimal(pips).dividedBy(0.01);
            } else {
                $scope.selectedTrade.pips = new Decimal(pips).dividedBy(0.0001);
            }

            if ($scope.selectedTrade.index != undefined) {
                $scope.trades[$scope.selectedTrade.index] = $scope.selectedTrade;
            } else {
                $scope.trades.push($scope.selectedTrade);
            }

            $("#addTrade").modal("hide");
            $scope.newTrade();
            $scope.saveTrades();
        };

        $scope.sortType = 'date';
        $scope.sortReverse = true;

        $scope.limitOptions = [5, 10, 15, 25, 50, 100];
        $scope.limit = 10;
        $scope.page = 0;

        $scope.selectedTrade = {};
        $scope.dateInput = "";
        $scope.searchfilters = {
            "name": "",
            "type": ""
        };
        $scope.types = ["Sell", "Buy"];

        $scope.trades = $scope.getTrades();

        $scope.totalPips = $scope.getTotalPips();
        $scope.pipsTarget = 0;
    });
})();