<!DOCTYPE html>
<html lang="en-gb">
	<head>
		<meta charset="UTF-8" >
		<meta name="viewport" content="width=device-width, initial-scale=1" >

		<?php
		$appName = $pageTitle = "Trading Tracker";
		$pageTitle = "Online Tool for the Forex Market | {$pageTitle}";
		$pageDesc = "A online tool to track any executed trades in the Forex market, to aid in future planning and/or execution of trades";

		$requestedRelativeURL = isset($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : "";
		$requestedRelativeURL = parse_url($requestedRelativeURL, PHP_URL_PATH);
		$requestedRelativeURL = trim($requestedRelativeURL, " /");

		if (!empty($requestedRelativeURL)) {
			$requestedRelativeURL .= "/";
		}

		$liveDomain = "https://tradingtracker.000webhostapp.com/";
		$liveURL = rtrim($liveDomain, " /") . "/" . $requestedRelativeURL;

		$protocol = (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] != "off") ? "https" : "http";
		$localURL = "{$protocol}://" . rtrim($_SERVER["SERVER_NAME"], " /") . "/" . $requestedRelativeURL;

		$isProduction = $liveURL === $localURL;
		?>
		<title><?php echo $pageTitle; ?></title>

		<meta name="author" content="Jahidul Pabel Islam" />

		<meta name="description" content="<?php echo $pageDesc; ?>" />

		<meta property="og:locale" content="en_GB "/>
		<meta property="og:type" content="website" />
		<meta property="og:title" content="<?php echo $pageTitle; ?>" />
		<meta property="og:description" content="<?php echo $pageDesc; ?>" />
		<meta property="og:url" content="<?php echo $localURL; ?>" />
		<meta property="og:site_name" content="<?php echo $appName; ?>" />

		<meta name="twitter:title" content="<?php echo $pageTitle; ?>" />

		<?php
		if ($isProduction) {
			echo "<link rel='canonical' href='{$liveURL}' />";
		}
		else {
			echo "<meta name='robots' content='noindex,nofollow' />";
		}
		?>

		<?php
		if (isset($_GET["debug"])) {
			?>
			<link href="/assets/css/third-party/bootstrap.min.css?v=1" rel="stylesheet" title="style" media="all" type="text/css" />
			<link href="/assets/css/trading-tracker/style.css?v=1.4.1" rel="stylesheet" title="style" media="all" type="text/css" />
			<?php
		}
		else {
			?>
			<!-- Complied CSS File of all CSS Files -->
			<link href="assets/css/main.min.css?v=1.4.1" rel="stylesheet" title="style" media="all" type="text/css" />
			<?php
		}
		?>

		<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css?v=1" rel="stylesheet" />
	</head>

	<body ng-app="TradingTrackerApp">
		<div ng-controller="ctrl">
			<nav class="navbar navbar-dark bg-dark">
				<a class="navbar-brand" href="#">Trading Tracker</a>
			</nav>

			<main role="main" class="container">

				<div class="form-group add-trade-trigger-wrapper">
					<button type="button" class="btn btn-success" data-toggle="modal" data-target="#trade-form-modal" ng-click="newTrade()">Add a Trade</button>
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
							<option ng-repeat="x in dateOptions" value="{{ x }}">{{ x | date: "dd/MM/yyyy" }}</option>
						</select>
					</div>

					<div class="form-group col-6 col-md-3">
						<label for="filters__trade-type">Trade Type: </label>
						<select class="form-control" ng-model="searchfilters.type" id="filters__trade-type" ng-change="setPage(0); update();">
							<option value="" selected>Select Trade Type</option>
							<option ng-repeat="x in types" value="{{ x }}">{{x}}</option>
						</select>
					</div>

					<div class="form-group col-6 col-md-3">

						<label for="filters__items-limit">Per Page: </label>
						<select ng-model="limit" ng-options="x for x in limitOptions" id="filters__items-limit" class="form-control" ng-change="setPage(0); update();"></select>
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
									<span ng-show="sortType == 'name'" class="fa order-by" ng-class="sortReverse == true ? 'fa-caret-up' : 'fa-caret-down'"></span>
								</th>
								<th scope="col" class="sort-by" ng-click="setSortBy('date')">
									Date
									<span ng-show="sortType == 'date'" class="fa order-by" ng-class="sortReverse == true ? 'fa-caret-up' : 'fa-caret-down'"></span>
								</th>
								<th scope="col" class="sort-by" ng-click="setSortBy('type')">
									Type
									<span ng-show="sortType == 'type'" class="fa order-by" ng-class="sortReverse == true ? 'fa-caret-up' : 'fa-caret-down'"></span>
								</th>
								<th scope="col" class="sort-by" ng-click="setSortBy('pips')">
									Pips
									<span ng-show="sortType == 'pips'" class="fa order-by" ng-class="sortReverse == true ? 'fa-caret-up' : 'fa-caret-down'"></span>
								</th>
								<th scope="col" class="no-padding">-</th>
							</tr>
						</thead>
						<tbody>
							<tr ng-repeat="trade in trades | orderBy : sortType : sortReverse | filter : searchfilters | filter : dateFilter | limitTo : limit : page track by $index " class="trades__trade">
								<td data-title="Pair">{{trade.name}}</td>
								<td data-title="Date">{{trade.date | date: "dd/MM/yyyy"}}</td>
								<td data-title="Type">{{trade.type}}</td>
								<td data-title="Pips">{{trade.pips}}</td>
								<td class="no-padding no-title">
									<button type="button" class="btn btn-primary btn--view-trade" ng-click="selectTrade(trade)">View</button>
									<button type="button" class="btn btn-danger btn--delete-trade" ng-click="deleteTrade(trade)">x</button>
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
							<p class="page-link" ng-click="setPage(pageNum)">{{pageNum + 1}}</p>
						</li>

						<li class="page-item" ng-show="page < (filteredTrades.length / limit - 1)" ng-click="setPage(page + 1)">
							<p class="page-link">Next</p>
						</li>

						<li class="page-item" ng-show="page < (filteredTrades.length / limit - 1)" ng-click="setPage(filteredTrades.length / limit - 1 | number : 0)">
							<p class="page-link">Last</p>
						</li>
					</ul>
				</nav>
			</main>

			<footer class="footer">
				<div class="container">
					<?php
						$version = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/assets/version.txt');

						$origTimeZone = date_default_timezone_get();
						if (!empty($version)) {
							echo "<p>" . $version . "</p>";
						}
						date_default_timezone_set("Europe/London");
					?>
					<p>&copy; <a href="https://jahidulpabelislam.com/">Jahidul Pabel Islam</a> <?php echo date("Y"); ?></p>
					<?php date_default_timezone_set($origTimeZone); ?>
					<p>Team <a href="https://www.jkmt.co.uk/">#JKMT</a></p>
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
										<option ng-repeat="x in types" value="{{ x }}">{{x}}</option>
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
									<textarea ng-model="selectedTrade.notes" id="notesInput" class="form-control" placeholder="Saw a down trend on 2hr and ..." rows="6"></textarea>
								</div>
							</div>
							<div class="modal-footer">
								<button type="submit" class="btn btn-success">{{ selectedTrade.index != undefined ? "Update" : "Add" }}</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>

		<?php if (isset($_GET["debug"])): ?>
			<script src="/assets/js/third-party/jquery-3.2.1.min.js?v=1" type="application/javascript"></script>
			<script src="/assets/js/third-party/popper.min.js?v=1" type="application/javascript"></script>
			<script src="/assets/js/third-party/angular.min.js?v=1" type="application/javascript"></script>
			<script src="/assets/js/third-party/decimal.min.js?v=1" type="application/javascript"></script>
			<script src="/assets/js/trading-tracker/stickyFooter.js?v=1" type="application/javascript"></script>
		<?php else: ?>
			<!-- Complied JavaScript File of all JavaScript Files -->
			<script src="/assets/js/main.min.js?v=1" type="application/javascript"></script>
		<?php endif; ?>

		<script src="/assets/js/third-party/bootstrap.min.js?v=1" type="application/javascript"></script>
		<script src="/assets/js/trading-tracker/app.js?v=1.4.1" type="application/javascript"></script>
	</body>
</html>