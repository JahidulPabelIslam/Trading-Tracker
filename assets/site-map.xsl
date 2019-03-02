<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9" version="1.0">
    <xsl:template match="/">
        <html>
            <head>
                <!--The Title Page -->
                <title>Trading Tracker XML Site Map</title>

                <!-- CSS, Styling the page using CSS -->
                <style type="text/css">
                    body {
                        font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
                    }

                    h1 {
                        text-align: center;
                    }

                    a {
                        color: #ffd700;
                    }

                    table {
                        padding: 0;
                        margin: auto;
                        min-width: 400px;
                        max-width: 100%;
                        border: 2px solid black;
                    }

                    th,
                    td{
                        padding: 4px;
                    }

                    thead tr {
                        background: #ffd700;
                    }

                    tbody th {
                        text-align: left;
                    }

                    table a {
                        color: #333;
                    }

                    table a:hover {
                        color: black;
                    }
                </style>
            </head>

            <body>
                <!-- Heading for page -->
                <h1>Trading Tracker XML Site Map</h1>

                <!-- A Table to hold the links -->
                <table>
                    <thead>
                        <tr>
                            <th>URL</th>
                        </tr>
                    </thead>

                    <tbody>
                        <!-- Loop through each page in set -->
                        <xsl:for-each select="sitemap:urlset/sitemap:url">
                            <!-- Create a row -->
                            <tr>
                                <td>
                                    <!-- Create a 'a' tag -->
                                    <a>
                                        <xsl:attribute name="href">
                                            <!-- Make href of a tag the link of page -->
                                            <xsl:value-of select="sitemap:loc"/>
                                        </xsl:attribute>
                                        <!-- Make link text the link of page -->
                                        <xsl:value-of select="sitemap:loc"/>
                                    </a>
                                </td>
                            </tr>
                        </xsl:for-each>
                    </tbody>
                </table>
            </body>
        </html>
    </xsl:template>
</xsl:stylesheet>