marsis
======

MARSIS Web Client
Web application for the visualization of radargrams and surface clutter 
simulations (if available) from planetserver/marsis db

The 2D GUI is based on the classic client of planetserver (http://planetserver.eu/classic/)

# Requirements
- Web server (used with Apache on http://marsis.planetserver.eu)
- PHP5
- PHP5-pgsql
- PostgreSQL
- PostGIS (see https://github.com/planetserver/server-ingestion-marsis/INSTALL)
- RasDaMan 9 (see https://github.com/planetserver/server-ingestion-marsis/INSTALL)

Required JavaScrips libs are included in the source


# Installation
Copy the files in the web server (properly configured) documents directory of your choice

No changes in the files are needed as the server is created and populated using 
the scripts in https://github.com/planetserver/server-ingestion-marsis.

If needed, proper db name and user can be set in index.php and showdata.php

# Contributors

Federico Cantini
