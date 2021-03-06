;(function(angular, jQuery, Decimal, StickyFooter) {

    "use strict";

    window.tradingTracker = window.tradingTracker || {};

    var app = angular.module("TradingTrackerApp", []);

    app.controller("ctrl", function($scope, $filter) {

        var global = {
            tradesStoreKey: "tradingtrackertrades",
            targetsStoreKey: "tradingtrackertargets",
        };

        var fn = {

            resetFooter: function() {
                if (tradingTracker && tradingTracker.stickyFooter) {
                    // Slight delay so Angular updates UI
                    setTimeout(function() {
                        tradingTracker.stickyFooter.repositionFooter();
                    }, 1);
                }
            },

            getFromLocalStorage: function(key) {
                return JSON.parse(localStorage.getItem(key));
            },

            saveToLocalStorage: function(key, data) {
                localStorage.setItem(key, JSON.stringify(data));
            },

            addColourClassByPercentage: function(percentage, selectors) {
                var classesToAdd = "way-off-target";
                if (percentage > 150) {
                    classesToAdd = "beyond-target";
                }
                else if (percentage > 100) {
                    classesToAdd = "above-target";
                }
                else if (percentage == 100) {
                    classesToAdd = "on-target";
                }
                else if (percentage > 80) {
                    classesToAdd = "close-to-target";
                }
                else if (percentage > 50) {
                    classesToAdd = "off-target";
                }

                var classesToRemove = "way-off-target off-target close-to-target on-target above-target beyond-target";

                jQuery(selectors).removeClass(classesToRemove).addClass(classesToAdd);
            },

            getPipTargets: function() {
                var targets = fn.getFromLocalStorage(global.targetsStoreKey);

                if (targets) {
                    return targets;
                }

                return {
                    "": {},
                    "Buy": {},
                    "Sell": {},
                };
            }
        };

        $scope.setPage = function(newPageNum) {
            $scope.currentPage = newPageNum;
            fn.resetFooter();
        };

        $scope.setSortBy = function(sortBy) {
            if (sortBy === $scope.sortBy) {
                $scope.isSortReverse = !$scope.isSortReverse;
            }
            else {
                $scope.sortBy = sortBy;
            }

            $scope.setPage(1);
            $scope.getAndUpdateValues();
        };

        $scope.getTrades = function() {
            var trades = fn.getFromLocalStorage(global.tradesStoreKey);

            if (trades) {
                return $scope.sortTrades(trades);
            }

            return [];
        };

        $scope.newTrade = function() {
            $scope.selectedTrade = {};
        };

        $scope.saveTrades = function() {
            fn.saveToLocalStorage(global.tradesStoreKey, $scope.trades);
            $scope.getAndUpdateValues();
        };

        $scope.saveTrade = function() {
            var isUpdateTrade = $scope.selectedTrade.isOld;

            $scope.selectedTrade.lot = parseFloat($scope.selectedTrade.lot);
            $scope.selectedTrade.date = $scope.selectedTrade.dateObj.toISOString();

            delete $scope.selectedTrade.index;
            delete $scope.selectedTrade.isOld;
            delete $scope.selectedTrade.dateObj;

            if (!isUpdateTrade) {
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
            $scope.selectedTrade = trade;
            $scope.selectedTrade.isOld = true;
            $scope.selectedTrade.dateObj = new Date(trade.date);

            jQuery("#trade-form-modal").modal("show");
        };

        $scope.dateFilter = function(trade) {
            var dateFilterValue = $scope.dateFilterInput;
            if (!dateFilterValue || dateFilterValue === "") {
                return true;
            }
            else {
                var tradeDate = new Date(trade.date);
                tradeDate.setHours(0, 0, 0, 0);
                var tradeDateTime = tradeDate.getTime();

                if (
                    dateFilterValue === "This Week" ||
                    dateFilterValue === "This Month" ||
                    dateFilterValue === "This Year"
                ) {
                    var firstDay = new Date();
                    firstDay.setHours(0, 0, 0, 0);
                    var lastDay = new Date(firstDay);

                    if (dateFilterValue === "This Week") {
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
                    else if (dateFilterValue === "This Month") {
                        // Get beginning of the current month
                        firstDay.setDate(1);

                        // Get last day of the current month
                        lastDay.setMonth(lastDay.getMonth() + 1);
                        lastDay.setDate(lastDay.getDate() - 1);
                    }
                    else if (dateFilterValue === "This Year") {
                        // Get beginning of the first month of the year
                        firstDay.setDate(1);
                        firstDay.setMonth(1);

                        // Get last day of the last month of the year
                        lastDay.setMonth(12);
                        lastDay.setDate(31);
                    }

                    return (
                        (tradeDateTime >= firstDay.getTime()) &&
                        (tradeDateTime <= lastDay.getTime())
                    );
                }

                var matchDate = new Date();
                matchDate.setHours(0, 0, 0, 0);
                if (dateFilterValue === "Today") {
                    // NOP
                }
                else if (dateFilterValue === "Yesterday") {
                    matchDate.setDate(matchDate.getDate() - 1);
                }
                // This leaves an actual date option left
                else {
                    matchDate = new Date(dateFilterValue);
                    matchDate.setHours(0, 0, 0, 0);
                }

                return matchDate.getTime() === tradeDateTime;
            }
        };

        $scope.sortTrades = function(trades) {
            trades = $filter("orderBy")(trades, $scope.sortBy, $scope.isSortReverse);
            return trades;
        };

        $scope.getFilteredTrades = function() {
            var filteredTrades = $filter("filter")($scope.trades, $scope.searchFilters);
            filteredTrades = $filter("filter")(filteredTrades, $scope.dateFilter);

            var sortedTrades = $scope.sortTrades(filteredTrades);

            return sortedTrades;
        };

        $scope.getTotalPips = function() {
            var trades = $scope.filteredTrades;
            var pips = new Decimal(0);

            for (var i = 0; i < trades.length; i++) {
                var trade = trades[i];
                pips = pips.add(trade.pips);
            }

            pips = parseFloat(pips);

            return pips;
        };

        $scope.calculatePips = function() {
            var entryPrice = $scope.selectedTrade.entryprice = parseFloat($scope.selectedTrade.entryprice);
            var exitPrice = $scope.selectedTrade.exitprice = parseFloat($scope.selectedTrade.exitprice);

            var pips = 0;
            if ($scope.selectedTrade.type === "Buy") {
                pips = new Decimal(exitPrice).minus(entryPrice);
            }
            else {
                pips = new Decimal(entryPrice).minus(exitPrice);
            }

            var name = $scope.selectedTrade.name.toLowerCase();

            if (name.includes("jpy") || name.includes("xau")) {
                pips = pips.dividedBy(0.01);
            }
            else {
                pips = pips.dividedBy(0.0001);
            }

            pips = parseFloat(pips);

            $scope.selectedTrade.pips = pips;

            return pips;
        };

        $scope.getPipsTarget = function() {
            var pipsTarget = parseFloat($scope.pipsTarget);
            if (!pipsTarget || pipsTarget < 0) {
                if (isNaN(pipsTarget) && $scope.pipsTarget !== null) {
                    $scope.pipsTarget = 0;
                }

                pipsTarget = 0;
            }

            return pipsTarget;
        };

        $scope.getPipsTargetForFilter = function() {
            if (
                $scope.pipsTargets &&
                $scope.pipsTargets[$scope.searchFilters.type] &&
                $scope.pipsTargets[$scope.searchFilters.type][$scope.dateFilterInput] &&
                $scope.pipsTargets[$scope.searchFilters.type][$scope.dateFilterInput][$scope.searchFilters.name]
            ) {
                return $scope.pipsTargets[$scope.searchFilters.type][$scope.dateFilterInput][$scope.searchFilters.name];
            }

            return 0;
        };

        $scope.savePipsTarget = function() {
            if (!$scope.pipsTargets[$scope.searchFilters.type]) {
                $scope.pipsTargets[$scope.searchFilters.type] = {};
            }

            if (!$scope.pipsTargets[$scope.searchFilters.type][$scope.dateFilterInput]) {
                $scope.pipsTargets[$scope.searchFilters.type][$scope.dateFilterInput] = {};
            }

            if (!$scope.pipsTargets[$scope.searchFilters.type][$scope.dateFilterInput]) {
                $scope.pipsTargets[$scope.searchFilters.type][$scope.dateFilterInput] = {};
            }

            $scope.pipsTargets[$scope.searchFilters.type][$scope.dateFilterInput][$scope.searchFilters.name] = $scope.getPipsTarget();

            fn.saveToLocalStorage(global.targetsStoreKey, $scope.pipsTargets);
        };

        $scope.updatePipsCounterColours = function() {
            var percentage = new Decimal($scope.totalPips).dividedBy($scope.getPipsTarget()).times(100);
            fn.addColourClassByPercentage(percentage, ".counters__pips-won, .counters__pips-remaining");
        };

        $scope.getPipsRemaining = function() {
            var totalPipsWon = $scope.totalPips;

            var pipsTarget = $scope.getPipsTarget();

            var pipsRemaining = new Decimal(pipsTarget).minus(totalPipsWon);
            pipsRemaining = parseFloat(pipsRemaining);

            return pipsRemaining;
        };

        $scope.getWinToLoss = function() {
            var trades = $scope.filteredTrades;
            var numOfTrades = trades.length;
            if (!numOfTrades) {
                return "N/A";
            }

            var wins = 0;

            for (var i = 0; i < numOfTrades; i++) {
                var trade = trades[i];

                if (trade.pips > 0) {
                    wins++;
                }
            }

            wins = new Decimal(wins);

            var winPercentage = wins.dividedBy(numOfTrades).times(100);
            winPercentage = winPercentage.toFixed(2);

            fn.addColourClassByPercentage(winPercentage, ".counters__win-loss");

            return winPercentage + "%";
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

        $scope.getLastPageNum = function() {
            var totalTrades = $scope.filteredTrades.length;
            return Math.ceil(totalTrades / $scope.limitTo);
        };

        $scope.getAndUpdateValues = function() {
            $scope.pipsTarget = $scope.getPipsTargetForFilter();

            $scope.filteredTrades = $scope.getFilteredTrades();

            $scope.lastPageNum = $scope.getLastPageNum();

            $scope.totalPips = $scope.getTotalPips();
            $scope.pipsRemaining = $scope.getPipsRemaining();

            $scope.updatePipsCounterColours();

            $scope.dateFilterOptions = $scope.getDateOptions();

            $scope.winToLoss = $scope.getWinToLoss();

            fn.resetFooter();
        };

        $scope.updateCounters = function() {
            $scope.pipsRemaining = $scope.getPipsRemaining();
            $scope.updatePipsCounterColours();
        };

        $scope.init = function() {
            $scope.pipsTargets = fn.getPipTargets();
            $scope.pipsTarget = 0;

            $scope.tradeTypes = ["Sell", "Buy"];

            $scope.searchFilters = {
                name: "",
                type: "",
            };

            $scope.sortBy = "date";
            $scope.isSortReverse = true;

            $scope.limitToOptions = [10, 30, 50, 100];
            $scope.limitTo = 30;
            $scope.currentPage = 1;

            $scope.trades = $scope.getTrades();

            $scope.dateFilterOptions = $scope.getDateOptions();
            $scope.dateFilterInput = "";

            $scope.getAndUpdateValues();

            $scope.selectedTrade = {};
        };

        jQuery(window).on("load", function() {
            tradingTracker.stickyFooter = new StickyFooter(".main-content");

            /**
             * Slight hack to remove 000webhost ad from DOM.
             * We know its the only other 'div' elem as a child of 'body'
             * other than our main div ('class=content-wrapper')
             * So we target this with this knowledge and remove
             */
            jQuery("body > div:not(.content-wrapper)").remove();
        });

        $scope.init();
    });

})(angular, jQuery, Decimal, StickyFooter);
