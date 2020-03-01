# Installation

First of all, we need to create a database which is named `restfull_api`

Once the database is created, run the following URL command to install tables and seeder.

`127.0.0.1/installation.php`

Our database is all set!

We've configured a **PSR-4** autoloader which will automatically look for PHP classes in the **/src** directory.

We can install our dependencies now:

`composer install`

Copy **.env.example**

Change the name with .env

Make your own settings in .env file.

* **DB_HOST=localhost**
* **DB_PORT=3306**
* **DB_DATABASE=restfull_api**
* **DB_USERNAME=root**
* **DB_PASSWORD=password**

# Run

You can test the API with a tool like Postman. First, go to the project directory and start the PHP server:

`php -S 127.0.0.1:8000 -t public`

The results output returns in json or xml format.

**URL FORMAT**

`http://127.0.0.1:8000/{content_type}?mmsi={integet}&minLat={decimal}&maxLat={decimal}`

**Examples/Tests**

Includes all filters such as mmsi, minLat, maxLat as XML format

`http://127.0.0.1:8000/xml?mmsi=247039300&minLat=41.9483&maxLat=42.3444`

Includes all filters such as mmsi, minLon, maxLon as XML format

`http://127.0.0.1:8000/xml?mmsi=247039300&minLon=16.28206&maxLon=16.2957`

Includes multiple mmsi as json format

`http://127.0.0.1:8000/json?mmsi[]=311486000&mmsi[]=311486000`

# Vessels Tracks API

Your task is to create a **RESTful API** that serves vessel tracks from a raw vessel positions data-source.
The raw data is supplied as a JSON file that you must import to a database schema of your choice.

Fields supplied are:
* **mmsi**: unique vessel identifier
* **status**: AIS vessel status
* **station**: receiving station ID
* **speed**: speed in knots x 10 (i.e. 10,1 knots is 101)
* **lon**: longitude
* **lat**: latitude
* **course**: vessel's course over ground
* **heading**: vessel's true heading
* **rot**: vessel's rate of turn
* **timestamp**: position timestamp

**The API end-point must:**
* Support the following filters: 
  * **mmsi** (single or multiple)
  * **latitude** and **longitude range** (eg: minLat=1&maxLat=2&minLon=3&maxLon=4)
  * as well as **time interval**.
* Log incoming requests to a datastore of  your choice (plain text, database, third party service etc.)
* Limit requests per user to **10/hour**. (Use the request remote IP as a user identifier)
* Support the following content types:
  * At least two of the following: application/json, application/vnd.api+json, application/ld+json, application/hal+json
  * application/xml
  * text/csv
* Don't forget to include your tests

**Share your work:**
* Stage your solution on a demo page or
* Fork this repo and create a pull request that contains your implementation in a new branch named after you.

**Have fun!**
