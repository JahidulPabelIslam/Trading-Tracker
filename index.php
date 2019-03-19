<?php
$serverData = $_SERVER;
include_once(rtrim($serverData["DOCUMENT_ROOT"], "/") . "/App.php");
$app = App::get();
?>

<!DOCTYPE html>
<html lang="en-gb">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <?php
        $isDebug = $app->isDebug();

        $appName = App::APP_NAME;
        $pageTitle = "{$appName} - Online Tracking Tool for the Forex Market";
        $pageDesc = "A online tool to track any executed trades in the Forex market, to aid in future planning and/or execution of trades";

        $liveURL = $app->getLiveURL();
        $localURL = $app->getLocalURL();

        $isProduction = $liveURL === $localURL;

        if ($isProduction) {
            ?>
            <!-- Global site tag (gtag.js) - Google Analytics -->
            <script async src="https://www.googletagmanager.com/gtag/js?id=UA-70803146-3"></script>
            <script>
                window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag("js",new Date());gtag("config","UA-70803146-3");
            </script>
            <?php
        }
        ?>

        <title><?php echo $pageTitle; ?></title>

        <meta name="author" content="Jahidul Pabel Islam" />

        <meta name="description" content="<?php echo $pageDesc; ?>" />

        <meta property="og:locale" content="en_GB" />
        <meta property="og:type" content="website" />
        <meta property="og:site_name" content="<?php echo $appName; ?>" />
        <meta property="og:title" content="<?php echo $pageTitle; ?>" />
        <meta property="og:description" content="<?php echo $pageDesc; ?>" />
        <meta property="og:url" content="<?php echo $localURL; ?>" />

        <meta property="og:image" content="<?php $app->addVersion("/assets/images/og-home-image.jpg"); ?>" />
        <meta property="og:image:width" content="1145" />
        <meta property="og:image:height" content="599" />

        <meta name="twitter:title" content="<?php echo $pageTitle; ?>" />

        <?php
        if ($isProduction) {
            echo "<link rel='canonical' href='{$liveURL}' />";
        }
        else {
            echo "<meta name='robots' content='noindex,nofollow' />";
        }
        ?>

        <link rel="apple-touch-icon" sizes="57x57" href="<?php $app->addVersion("/assets/favicons/apple-touch-icon-57x57.png"); ?>" />
        <link rel="apple-touch-icon" sizes="60x60" href="<?php $app->addVersion("/assets/favicons/apple-touch-icon-60x60.png"); ?>" />
        <link rel="apple-touch-icon" sizes="72x72" href="<?php $app->addVersion("/assets/favicons/apple-touch-icon-72x72.png"); ?>" />
        <link rel="apple-touch-icon" sizes="76x76" href="<?php $app->addVersion("/assets/favicons/apple-touch-icon-76x76.png"); ?>" />
        <link rel="apple-touch-icon" sizes="114x114" href="<?php $app->addVersion("/assets/favicons/apple-touch-icon-114x114.png"); ?>" />
        <link rel="apple-touch-icon" sizes="120x120" href="<?php $app->addVersion("/assets/favicons/apple-touch-icon-120x120.png"); ?>" />
        <link rel="apple-touch-icon" sizes="144x144" href="<?php $app->addVersion("/assets/favicons/apple-touch-icon-144x144.png"); ?>" />
        <link rel="apple-touch-icon" sizes="152x152" href="<?php $app->addVersion("/assets/favicons/apple-touch-icon-152x152.png"); ?>" />
        <link rel="apple-touch-icon" sizes="180x180" href="<?php $app->addVersion("/assets/favicons/apple-touch-icon-180x180.png"); ?>" />
        <link rel="icon" type="image/png" sizes="32x32" href="<?php $app->addVersion("/assets/favicons/favicon-32x32.png"); ?>" />
        <link rel="icon" type="image/png" sizes="194x194" href="<?php $app->addVersion("/assets/favicons/favicon-194x194.png"); ?>" />
        <link rel="icon" type="image/png" sizes="192x192" href="<?php $app->addVersion("/assets/favicons/android-chrome-192x192.png"); ?>" />
        <link rel="icon" type="image/png" sizes="16x16" href="<?php $app->addVersion("/assets/favicons/favicon-16x16.png"); ?>" />
        <link rel="manifest" href="<?php $app->addVersion("/assets/favicons/site.webmanifest"); ?>" />
        <link rel="mask-icon" href="<?php $app->addVersion("/assets/favicons/safari-pinned-tab.svg"); ?>" color="#ffd700" />
        <link rel="shortcut icon" href="<?php $app->addVersion("favicon.ico"); ?>" />
        <meta name="apple-mobile-web-app-title" content="<?php echo $appName; ?>" />
        <meta name="application-name" content="<?php echo $appName; ?>" />
        <meta name="msapplication-TileColor" content="#ffd700" />
        <meta name="msapplication-TileImage" content="<?php $app->addVersion("/assets/favicons/mstile-144x144.png"); ?>" />
        <meta name="msapplication-config" content="<?php $app->addVersion("/assets/favicons/browserconfig.xml"); ?>" />
        <meta name="theme-color" content="#343a40" />

        <?php
        if ($isDebug) {
            ?>
            <link href="<?php $app->addVersion("/assets/css/third-party/bootstrap.min.css"); ?>" rel="stylesheet" title="style" media="all" type="text/css" />
            <link href="<?php $app->addVersion("/assets/css/trading-tracker/style.css"); ?>" rel="stylesheet" title="style" media="all" type="text/css" />
            <?php
        }
        else {
            ?>
            <!-- Complied CSS File of all CSS Files -->
            <link href="<?php $app->addVersion("assets/css/main.min.css"); ?>" rel="stylesheet" title="style" media="all" type="text/css" />
            <?php
        }
        ?>

        <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet" />
    </head>

    <body ng-app="TradingTrackerApp">
        <div ng-controller="ctrl">
            <nav class="navbar navbar-dark bg-dark">
                <div class="container">
                    <a class="navbar-brand" href="#">
                        <img src="<?php $app->addVersion("/assets/images/logo.png"); ?>" alt="<?php echo $appName; ?> Logo" class="navbar__logo" />
                        <img src="<?php $app->addVersion("/assets/images/app-name.png"); ?>" alt="<?php echo $appName; ?> text" class="navbar__app-name" />
                    </a>
                </div>
            </nav>

            <main role="main" class="main-content">
                <div class="container">
                    <div class="form-group add-trade-trigger-wrapper">
                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#trade-form-modal" ng-click="newTrade()">
                            Add a Trade
                        </button>
                    </div>

                    <div class="row filters">

                        <div class="form-group col-6 col-md-3">
                            <label for="filters__pair-name">Pair</label>
                            <input ng-model="searchfilters.name" type="text" placeholder="Enter Pair Name (EURUSD)" class="form-control" id="filters__pair-name" ng-change="setPage(0); update()" />
                        </div>

                        <div class="form-group col-6 col-md-3">
                            <label for="filters__date">Date</label>
                            <select class="form-control" ng-model="dateInput" id="filters__date" ng-change="setPage(0); update();">
                                <option value="" selected>Select Date</option>
                                <option ng-repeat="x in dateOptions" value="{{ x }}">
                                    {{ x | date: "dd/MM/yyyy" }}
                                </option>
                            </select>
                        </div>

                        <div class="form-group col-6 col-md-3">
                            <label for="filters__trade-type">Trade Type: </label>
                            <select class="form-control" ng-model="searchfilters.type" id="filters__trade-type" ng-change="setPage(0); update();">
                                <option value="" selected>Select Trade Type</option>
                                <option ng-repeat="x in types" value="{{ x }}">
                                    {{ x }}
                                </option>
                            </select>
                        </div>

                        <div class="form-group col-6 col-md-3">

                            <label for="filters__items-limit">Per Page: </label>
                            <select ng-model="limit" ng-options="x for x in limitOptions" id="filters__items-limit" class="form-control" ng-change="setPage(0); update();">
                            </select>
                        </div>
                    </div>

                    <div class="row pips-count">

                        <label class="form-group col-2 col-md-2" for="pips-count__target">Pips Target: </label>
                        <div class="form-group col-4 col-md-2">
                            <input ng-model="pipsTarget" type="number" min="0.00" step="any" placeholder="60" class="form-control" id="pips-count__target" ng-change="updateCounters()" />
                        </div>

                        <label class="form-group col-2 col-md-2" for="pips-count__won">Pips Won: </label>
                        <div class="form-group col-4 col-md-2">
                            <input ng-value="totalPips" type="number" readonly class="form-control" id="pips-count__won" />
                        </div>

                        <label class="form-group col-2 col-md-2" for="pips-count__remaining">Pips Left: </label>
                        <div class="form-group col-4 col-md-2">
                            <input ng-value="pipsLeft" type="number" readonly class="form-control" id="pips-count__remaining" />
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table--trades">
                            <thead ng-show="filteredTrades.length > 0">
                                <tr>
                                    <th scope="col" class="sort-by" ng-click="setSortBy('name')">
                                        Pair
                                        <span ng-show="sortType == 'name'" class="fa order-by" ng-class="sortReverse == true ? 'fa-caret-up' : 'fa-caret-down'">
                                    </span>
                                    </th>
                                    <th scope="col" class="sort-by" ng-click="setSortBy('date')">
                                        Date
                                        <span ng-show="sortType == 'date'" class="fa order-by" ng-class="sortReverse == true ? 'fa-caret-up' : 'fa-caret-down'">
                                    </span>
                                    </th>
                                    <th scope="col" class="sort-by" ng-click="setSortBy('type')">
                                        Type
                                        <span ng-show="sortType == 'type'" class="fa order-by" ng-class="sortReverse == true ? 'fa-caret-up' : 'fa-caret-down'">
                                    </span>
                                    </th>
                                    <th scope="col" class="sort-by" ng-click="setSortBy('pips')">
                                        Pips
                                        <span ng-show="sortType == 'pips'" class="fa order-by" ng-class="sortReverse == true ? 'fa-caret-up' : 'fa-caret-down'">
                                    </span>
                                    </th>
                                    <th scope="col" class="no-padding">-</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="trade in getFilteredTrades() | limitTo : limit : page track by $index " class="trades__trade">
                                    <td data-title="Pair">{{ trade.name }}</td>
                                    <td data-title="Date">{{ trade.date | date: "dd/MM/yyyy" }}</td>
                                    <td data-title="Type">{{ trade.type }}</td>
                                    <td data-title="Pips">{{ trade.pips }}</td>
                                    <td class="no-padding no-title">
                                        <button type="button" class="btn btn-primary btn--view-trade" ng-click="selectTrade(trade)">
                                            View
                                        </button>
                                        <button type="button" class="btn btn-danger btn--delete-trade" ng-click="deleteTrade(trade)">
                                            x
                                        </button>
                                    </td>
                                </tr>
                                <tr ng-if="filteredTrades.length == 0">
                                    <td class="no-trades" colspan="9">No Trades Found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <nav aria-label="Trades list navigation" ng-show="filteredTrades.length > 0 && pages.length > 1">
                        <ul class="pagination justify-content-end">

                            <li ng-show="page != 0" class="page-item" ng-click="setPage(0)">
                                <p class="page-link">First</p>
                            </li>

                            <li ng-show="page != 0" class="page-item" ng-click="setPage(page - 1)">
                                <p class="page-link">Previous</p>
                            </li>

                            <li ng-repeat="pageNum in pages" class="page-item" ng-class="page == pageNum ? 'active' : ''" ng-click="setPage(pageNum)">
                                <p class="page-link" ng-click="setPage(pageNum)">{{ pageNum + 1 }}</p>
                            </li>

                            <li class="page-item" ng-show="page < (filteredTrades.length / limit - 1)" ng-click="setPage(page + 1)">
                                <p class="page-link">Next</p>
                            </li>

                            <li class="page-item" ng-show="page < (filteredTrades.length / limit - 1)" ng-click="setPage(filteredTrades.length / limit - 1 | number : 0)">
                                <p class="page-link">Last</p>
                            </li>
                        </ul>
                    </nav>
                </div>
            </main>

            <footer class="footer">
                <div class="container">
                    <?php
                    $version = file_get_contents($serverData['DOCUMENT_ROOT'] . '/assets/version.txt');

                    $origTimeZone = date_default_timezone_get();
                    if (!empty($version)) {
                        echo "<p>" . $version . "</p>";
                    }
                    date_default_timezone_set("Europe/London");
                    ?>
                    <p>&copy; <a href="https://jahidulpabelislam.com/">Jahidul Pabel Islam</a> <?php echo date("Y"); ?></p>
                    <?php date_default_timezone_set($origTimeZone); ?>
                </div>
            </footer>

            <div class="modal" tabindex="-1" role="dialog" id="trade-form-modal">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form ng-submit="saveTrade()">
                            <div class="modal-header">
                                <h5 class="modal-title">{{ selectedTrade.index != undefined ? "Update" : "Add" }} Trade</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group row col-12">
                                    <label for="pairInput" class="col-md-6">Pair Name: </label>
                                    <input ng-model="selectedTrade.name" type="text" id="pairInput" class="form-control col-md-6" placeholder="EURUSD" required ng-change="calculatePips()" />
                                </div>

                                <div class="form-group row col-12">
                                    <label for="dateInput" class="col-6">Date Traded: </label>
                                    <input ng-model="selectedTrade.date" type="date" id="dateInput" class="form-control col-md-6" placeholder="18/02/18" required />
                                </div>

                                <div class="form-group row col-12">
                                    <label for="lotInput" class="col-md-6">Lot Size: </label>
                                    <input ng-model="selectedTrade.lot" type="number" id="lotInput" class="form-control col-md-6" placeholder="0.01" required step="any" />
                                </div>

                                <div class="form-group row col-12">
                                    <label for="typeInput" class="col-md-6">Trade Type</label>
                                    <select ng-model="selectedTrade.type" id="typeInput" class="form-control col-md-6" required ng-change="calculatePips()">
                                        <option value="" selected>Select Trade Type</option>
                                        <option ng-repeat="x in types" value="{{ x }}">{{ x }}</option>
                                    </select>
                                </div>

                                <div class="form-group row col-12">
                                    <label for="entrypriceInput" class="col-md-6">Entry Price: </label>
                                    <input ng-model="selectedTrade.entryprice" type="number" id="entrypriceInput" class="form-control col-md-6" placeholder="1.1234" required ng-change="calculatePips()" step="any" />
                                </div>

                                <div class="form-group row col-12">
                                    <label for="exitpriceInput" class="col-md-6">Exit Price: </label>
                                    <input ng-model="selectedTrade.exitprice" type="number" id="exitpriceInput" class="form-control col-md-6" placeholder="1.4321" required ng-change="calculatePips()" step="any" />
                                </div>

                                <div class="form-group row col-12">
                                    <label for="pipsInput" class="col-md-6">Pips: </label>
                                    <input ng-model="selectedTrade.pips" type="number" id="pipsInput" class="form-control col-md-6" placeholder="0" readonly />
                                </div>

                                <div class="form-group row col-12">
                                    <label for="notesInput">Note(s): </label>
                                    <textarea ng-model="selectedTrade.notes" id="notesInput" class="form-control" placeholder="Saw a down trend on 2hr and ..." rows="6">
                                    </textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">
                                    {{ selectedTrade.index != undefined ? "Update" : "Add" }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <?php
        if ($isDebug) {
            ?>
            <script src="<?php $app->addVersion("/assets/js/third-party/jquery-3.2.1.min.js"); ?>" type="application/javascript"></script>
            <script src="<?php $app->addVersion("/assets/js/third-party/popper.min.js"); ?>" type="application/javascript"></script>
            <script src="<?php $app->addVersion("/assets/js/third-party/angular.min.js"); ?>" type="application/javascript"></script>
            <script src="<?php $app->addVersion("/assets/js/third-party/decimal.min.js"); ?>" type="application/javascript"></script>
            <script src="<?php $app->addVersion("/assets/js/trading-tracker/sticky-footer.js"); ?>" type="application/javascript"></script>
            <?php
        }
        else {
            ?>
            <!-- Complied JavaScript File of all JavaScript Files -->
            <script src="<?php $app->addVersion("/assets/js/main.min.js"); ?>" type="application/javascript"></script>
            <?php
        }
        ?>

        <script src="<?php $app->addVersion("/assets/js/third-party/bootstrap.min.js"); ?>" type="application/javascript"></script>
        <script src="<?php $app->addVersion("/assets/js/trading-tracker/app.js"); ?>" type="application/javascript"></script>
    </body>
</html>