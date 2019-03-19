;(function(angular, jQuery, Decimal) {
    "use strict";

    var app = angular.module("TradingTrackerApp", []);

    app.controller("ctrl", function($scope, $filter) {

        $scope.setPage = function(page) {
            $scope.page = page;
        };

        $scope.setSortBy = function($sortType) {
            if ($sortType === $scope.sortType) {
                $scope.sortReverse = !$scope.sortReverse;
            }
            else {
                $scope.sortType = $sortType;
            }

            $scope.setPage(0);
        };

        $scope.getTrades = function() {
            var trades = JSON.parse(localStorage.getItem("tradingtrackertrades"));

            if (trades) {
                trades = $scope.sortTrades(trades);
            }
            else {
                trades = [];
            }

            return trades;
        };

        $scope.newTrade = function() {
            $scope.selectedTrade = {};
        };

        $scope.saveTrades = function() {
            localStorage.setItem("tradingtrackertrades", JSON.stringify($scope.trades));
            window.tt.stickyFooter.expandSection();
            $scope.update();
        };

        $scope.saveTrade = function() {
            $scope.selectedTrade.lot = parseFloat($scope.selectedTrade.lot);

            if ($scope.selectedTrade.index !== undefined) {
                $scope.trades[$scope.selectedTrade.index] = $scope.selectedTrade;
            }
            else {
                $scope.trades.push($scope.selectedTrade);
            }

            jQuery("#trade-form-modal").modal("hide");
            $scope.newTrade();
            $scope.saveTrades();
        };

        $scope.deleteTrade = function(trade) {
            var index = $scope.trades.indexOf(trade);
            $scope.trades.splice(index, 1);
            $scope.saveTrades();
        };

        $scope.selectTrade = function(trade) {
            trade.date = new Date(trade.date);
            trade.index = $scope.trades.indexOf(trade);
            $scope.selectedTrade = trade;

            jQuery("#trade-form-modal").modal("show");
        };

        $scope.dateFilter = function(trade) {
            if ($scope.dateInput === "" || !$scope.dateInput) {
                return true;
            }
            else {
                var tradeDate = new Date(trade.date);
                tradeDate.setHours(0, 0, 0, 0);

                if ($scope.dateInput === "This Week" || $scope.dateInput === "This Month" || $scope.dateInput === "This Year") {
                    var firstDay = new Date();
                    firstDay.setHours(0, 0, 0, 0);
                    var lastDay = new Date(firstDay);

                    if ($scope.dateInput === "This Week") {
                        // Store mapping of how many days to take away from today to get beginning of the week
                        var mapping = {
                            0: 6,
                            1: 0,
                            2: 1,
                            3: 2,
                            4: 3,
                            5: 4,
                            6: 5,
                        };
                        firstDay.setDate(firstDay.getDate() - mapping[firstDay.getDay()]);

                        lastDay = new Date(firstDay);
                        lastDay.setDate(firstDay.getDate() + 6);
                    }
                    else if ($scope.dateInput === "This Month") {
                        // Get beginning of the current month
                        firstDay.setDate(1);

                        // Get last day of the current month
                        lastDay.setMonth(lastDay.getMonth() + 1);
                        lastDay.setDate(lastDay.getDate() - 1);
                    }
                    else if ($scope.dateInput === "This Year") {
                        // Get beginning of the first month of the year
                        firstDay.setDate(1);
                        firstDay.setMonth(1);

                        // Get last day of the last month of the year
                        lastDay.setMonth(12);
                        lastDay.setDate(31);
                    }

                    return (tradeDate.getTime() >= firstDay.getTime()) && (tradeDate.getTime() <= lastDay.getTime());
                }

                var matchDate = new Date();
                matchDate.setHours(0, 0, 0, 0);
                if ($scope.dateInput === "Today") {
                    // NOP
                }
                else if ($scope.dateInput === "Yesterday") {
                    matchDate.setDate(matchDate.getDate() - 1);
                }
                // This leaves an actual date option left
                else {
                    matchDate = new Date($scope.dateInput);
                    matchDate.setHours(0, 0, 0, 0);
                }

                return matchDate.getTime() === tradeDate.getTime();
            }
        };

        $scope.sortTrades = function(trades) {
            trades = $filter("orderBy")(trades, $scope.sortType, $scope.sortReverse);
            return trades;
        };

        $scope.getFilteredTrades = function() {
            var trades = $filter("filter")($scope.trades, $scope.searchfilters);
            trades = $filter("filter")(trades, $scope.dateFilter);
            trades = $scope.sortTrades(trades);

            $scope.filteredTrades = trades;
            return trades;
        };

        $scope.getTotalPips = function() {
            var trades = $scope.filteredTrades;
            var pips = 0;

            for (var i = 0; i < trades.length; i++) {
                var trade = trades[i];
                pips = new Decimal(pips).add(trade.pips);
            }

            pips = parseFloat(pips);

            $scope.totalPips = pips;

            return pips;
        };

        $scope.calculatePips = function() {
            var entryprice = $scope.selectedTrade.entryprice = parseFloat($scope.selectedTrade.entryprice);

            var exitprice = $scope.selectedTrade.exitprice = parseFloat($scope.selectedTrade.exitprice);

            var pips = 0;
            if ($scope.selectedTrade.type === "Buy") {
                pips = new Decimal(exitprice).minus(entryprice);
            }
            else {
                pips = new Decimal(entryprice).minus(exitprice);
            }

            pips = parseFloat(pips);

            var name = $scope.selectedTrade.name.toLowerCase();

            if (name.includes("jpy") || name.includes("xau")) {
                pips = new Decimal(pips).dividedBy(0.01);
            }
            else {
                pips = new Decimal(pips).dividedBy(0.0001);
            }

            pips = parseFloat(pips);

            $scope.selectedTrade.pips = pips;

            return pips;
        };

        $scope.updatePipsCounterColours = function() {
            var pipsLeft = $scope.pipsLeft;

            var totalGained = $scope.totalPips;

            var percent = new Decimal(pipsLeft).dividedBy(totalGained);
            percent = parseFloat(percent);
            percent *= 100;

            var classes = "form-control ";
            if (percent >= 50) {
                classes += "way-off-target";
            }
            else if (percent >= 25) {
                classes += "off-target";
            }
            else if (percent > 0) {
                classes += "close-to-target";
            }
            else if (percent === 0) {
                classes += "on-target";
            }
            else if (percent > -50) {
                classes += "above-target";
            }
            else {
                classes += "beyond-target";
            }

            jQuery("#pips-count__remaining, #pips-count__won").removeClass().addClass(classes);
        };

        $scope.getPipsLeft = function() {
            var totalGained = $scope.totalPips;

            var pipsTarget = parseFloat($scope.pipsTarget);
            if (!pipsTarget || pipsTarget < 0) {
                if (isNaN(pipsTarget) && $scope.pipsTarget !== null) {
                    $scope.pipsTarget = 0;
                }

                pipsTarget = 0;
            }

            var pipsLeft = new Decimal(pipsTarget).minus(totalGained);
            pipsLeft = parseFloat(pipsLeft);

            $scope.pipsLeft = pipsLeft;

            return pipsLeft;
        };

        $scope.getDateOptions = function() {
            var options = ["Today", "Yesterday", "This Week", "This Month", "This Year"];

            var trades = $scope.trades;

            for (var i = 0; i < trades.length; i++) {
                var trade = trades[i];

                if (!options.includes(trade.date)) {
                    options.push(trade.date);
                }
            }

            return options;
        };

        $scope.getPages = function() {
            var total = $scope.filteredTrades.length;
            var last = Math.ceil(total / $scope.limit);
            var pages = [];

            for (var i = 0; i < last; i++) {
                pages.push(i);
            }

            return pages;
        };

        $scope.update = function() {
            $scope.filteredTrades = $scope.getFilteredTrades();

            $scope.pages = $scope.getPages();

            $scope.totalPips = $scope.getTotalPips();
            $scope.pipsLeft = $scope.getPipsLeft();

            $scope.updatePipsCounterColours();

            $scope.dateOptions = $scope.getDateOptions();
        };

        $scope.updateCounters = function() {
            $scope.pipsLeft = $scope.getPipsLeft();
            $scope.updatePipsCounterColours();
        };

        $scope.sortType = "date";
        $scope.sortReverse = true;

        $scope.limitOptions = [5, 10, 15, 25, 50, 100];
        $scope.limit = 10;
        $scope.page = 0;

        $scope.selectedTrade = {};

        $scope.trades = $scope.getTrades();
        $scope.filteredTrades = $scope.getFilteredTrades();

        $scope.pages = $scope.getPages();

        $scope.pipsTarget = 0;
        $scope.totalPips = $scope.getTotalPips();
        $scope.pipsLeft = $scope.getPipsLeft();
        $scope.updatePipsCounterColours();

        $scope.dateOptions = $scope.getDateOptions();
        $scope.dateInput = "";

        $scope.searchfilters = {
            name: "",
            type: "",
        };
        $scope.types = ["Sell", "Buy"];
    });

}(angular, jQuery, Decimal));
