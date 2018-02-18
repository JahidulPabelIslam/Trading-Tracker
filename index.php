<!DOCTYPE html>
<html lang="en">
    <head>
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
        <nav class="navbar navbar-dark bg-dark">
            <a class="navbar-brand" href="#">Trading Tracker</a>
        </nav>

        <main role="main" class="container" ng-controller="ctrl">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Pair</th>
                        <th scope="col">Date</th>
                        <th scope="col">Lot</th>
                        <th scope="col">Type</th>
                        <th scope="col">Start Price</th>
                        <th scope="col">Finish Price</th>
                        <th scope="col">Total Pips</th>
                    </tr>
                </thead>
                <tbody>
                    <tr ng-repeat="trade in trades | filter : searchtxt">
                        <td>{{trade.name}}</td>
                        <td>{{trade.date}}</td>
                        <td>{{trade.lot}}</td>
                        <td>{{trade.type}}</td>
                        <td>{{trade.startprice}}</td>
                        <td>{{trade.endprice}}</td>
                        <td>{{trade.pips}}</td>
                    </tr>
                </tbody>
            </table>
        </main>

        <footer class="footer">
            <div class="container">
                <p>&copy; <a href="https://jahidulpabelislam.000webhostapp.com/">Jahidul Pabel Islam</a> 2018</p>
                <p>Team <a href="https://www.instagram.com/myriad.official/" class="myriad-text">#Myriad</a> / Team <a href="https://www.jkmt.co.uk/">#JKMT</a></p>
            </div>
        </footer>

        <?php if (isset($_GET["debug"])):?>
            <script src="/assets/js/third-party/jquery-3.2.1.min.js" type="application/javascript"></script>
            <script src="/assets/js/third-party/popper.min.js" type="application/javascript"></script>
            <script src="/assets/js/third-party/bootstrap.min.js" type="application/javascript"></script>
            <script src="/assets/js/third-party/angular.min.js" type="application/javascript"></script>
            <script src="/assets/js/trading-tracker/app.js" type="application/javascript"></script>
        <?php else: ?>
            <!-- Complied JavaScript File of all JavaScript Files -->
            <script src="/assets/js/main.min.js" type="application/javascript"></script>
        <?php endif; ?>
    </body>
</html>