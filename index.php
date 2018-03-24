<!DOCTYPE html>
<html lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <meta charset="UTF-8">
        <title>Trading Tracker</title>

        <?php if (isset($_GET["debug"])):?>
            <link href="/assets/css/third-party/bootstrap.min.css" rel="stylesheet" title="style" media="all" type="text/css">
            <link href="/assets/css/trading-tracker/style.css" rel="stylesheet" title="style" media="all" type="text/css">
        <?php else: ?>
            <!-- Complied CSS File of all CSS Files -->
            <link href="assets/css/main.min.css" rel="stylesheet" title="style" media="all" type="text/css">
        <?php endif; ?>
    </head>
    <body ng-app="TradingTrackerApp">
        <div ng-controller="ctrl">
            <nav class="navbar navbar-dark bg-dark">
                <a class="navbar-brand" href="#">Trading Tracker</a>
            </nav>

            <main role="main" class="container">

                <div class="form-group addTradeToggle">
                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#addTrade" ng-click="newTrade($index)">Add a Trade</button>
                </div>

                <div class="table-responsive">
                    <table class="table table--trades">
                        <thead>
                            <tr>
                                <th scope="col">Pair</th>
                                <th scope="col">Date</th>
                                <th scope="col">Type</th>
                                <th scope="col">Entry Price</th>
                                <th scope="col">Exit Price</th>
                                <th scope="col">Pips</th>
                                <th scope="col">Lot</th>
                                <th scope="col">-</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="trade in trades track by $index">
                                <td>{{trade.name}}</td>
                                <td>{{trade.date | date: "dd/MM/yyyy"}}</td>
                                <td>{{trade.type}}</td>
                                <td>{{trade.entryprice}}</td>
                                <td>{{trade.exitprice}}</td>
                                <td>{{trade.pips}}</td>
                                <td>{{trade.lot}}</td>
                                <td class="no-padding">
                                    <button type="button" class="btn btn-primary btn--edit-trade" ng-click="selectTrade($index)">Edit</button>
                                </td>
                                <td class="no-padding">
                                    <button type="button" class="btn btn-danger btn--delete-trade" ng-click="deleteTrade($index)">x</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </main>

            <footer class="footer">
                <div class="container">
                    <p>&copy; <a href="https://jahidulpabelislam.000webhostapp.com/">Jahidul Pabel Islam</a> 2018</p>
                    <p>Team <a href="https://www.jkmt.co.uk/">#JKMT</a> / Team <a href="https://www.instagram.com/myriad.official/" class="myriad-text">#Myriad</a></p>
                </div>
            </footer>

            <div class="modal" tabindex="-1" role="dialog" id="addTrade">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form ng-submit="addTrade()">
                            <div class="modal-header">
                                <h5 class="modal-title">Add Trade</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group row col-12">
                                    <label for="pairInput" class="col-md-6">Pair Name</label>
                                    <input ng-model="selectedTrade.name" type="text" id="pairInput" class="form-control col-md-6" placeholder="EURUSD" required>
                                </div>

                                <div class="form-group row col-12">
                                    <label for="dateInput" class="col-6">Date Traded</label>
                                    <input ng-model="selectedTrade.date" type="date" id="dateInput" class="form-control col-md-6" placeholder="18/02/18" required>
                                </div>

                                <div class="form-group row col-12">
                                    <label for="lotInput" class="col-md-6">Lot Size</label>
                                    <input ng-model="selectedTrade.lot" type="text"  id="lotInput" class="form-control col-md-6" placeholder="0.01" required>
                                </div>

                                <div class="form-group row col-12">
                                    <label for="typeInput" class="col-md-6">Trade Type</label>
                                    <select ng-model="selectedTrade.type" ng-options="x for x in types" id="typeInput" class="form-control col-md-6" required></select>
                                </div>

                                <div class="form-group row col-12">
                                    <label for="entrypriceInput" class="col-md-6">Entry Price</label>
                                    <input ng-model="selectedTrade.entryprice" type="text" id="entrypriceInput" class="form-control col-md-6" placeholder="1.1234" required>
                                </div>

                                <div class="form-group row col-12">
                                    <label for="exitpriceInput" class="col-md-6">Exit Price</label>
                                    <input ng-model="selectedTrade.exitprice" type="text" id="exitpriceInput" class="form-control col-md-6" placeholder="1.4321" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">{{selectedTrade.index != undefined ? 'Update' : 'Add'}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <?php if (isset($_GET["debug"])): ?>
            <script src="/assets/js/third-party/jquery-3.2.1.min.js" type="application/javascript"></script>
            <script src="/assets/js/third-party/popper.min.js" type="application/javascript"></script>
            <script src="/assets/js/third-party/bootstrap.min.js" type="application/javascript"></script>
            <script src="/assets/js/third-party/angular.min.js" type="application/javascript"></script>
            <script src="/assets/js/third-party/decimal.min.js" type="application/javascript"></script>
            <script src="/assets/js/trading-tracker/stickyFooter.js" type="application/javascript"></script>
            <script src="/assets/js/trading-tracker/app.js" type="application/javascript"></script>
        <?php else: ?>
            <!-- Complied JavaScript File of all JavaScript Files -->
            <script src="/assets/js/main.min.js" type="application/javascript"></script>
        <?php endif; ?>
    </body>
</html>