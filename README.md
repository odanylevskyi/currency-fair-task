CurrencyFair - Task
===================
###Message Consumption
**I have chosen Hard**
#####Goal
Expose an endpoint which can consume trade messages. Trade messages will be POST’d (assume by CurrencyFair during review) to this endpoint and will take the JSON form of:
```
{
    "userId": "134256", 
    "currencyFrom": "EUR", 
    "currencyTo": "GBP", 
    "amountSell": 1000, 
    "amountBuy": 747.10, 
    "rate": 0.7471, 
    "timePlaced" : "24-JAN-15 10:27:44", 
    "originatingCountry" : "FR"
}
```
#####Hard
The message consumption component is the main piece of work you focus on, and can handle a large number of messages per second.

###Message Processor
**I have chosen Avarage & Hard**
#####Goal
Process messages received via the message consumption endpoint. Depending on what you wish to do, these messages can be processed in different ways.
#####Average
Analyse incoming messages for trends, and transform data to prepare for a more visual frontend rendering, e.g. graphing currency volume of messages from one particular currency pair market (EUR/GBP).
#####Hard
Messages are sent through a realtime framework which pushes transformed data to a Socket.io frontend.

###Message Frontend
**I have chosen Avarage & Hard**
#####Goal
Render the data from the output of the other two components.
#####Average
Render a graph of processed data from the messages consumed.
#####Hard
Render a global map with a realtime visualisation of messages being processed.

APPROACH / SOLUTION
-------------------
- To solve current problem were chosen Yii2 Framework to manage all requests and render views.
- All frontend work is done using Bootstrap.
- All data was saved in Redis DB

###Message Consumption - Hard
I have chosen Redis DB to save posted data as Redis has good performance characteristics. 
All messages are saved in `messages` hSet(hash set). hSet gives constant time complexity for saving and retrieving data from Redis.
All saved messages can be processed one a day, hour, etc. and saved in, for example, MySQL/MariaDB to use it in future.
Data limitation: you can send as many data as you can. The only limitation is characteristics of the server.
To protect server from unauthorized data was decided to use tokens. You can use the next tokens for testing: 
```
5yKU4UIv9DQmO30LPW8ShMPkUXPAewrl
sgX6mvjmtOTN9ZSTTwZWN501IZeY95Ka
f9jP4ub9K5JOj920S6jy30o88oqi1uz7
```
Data should be sent to the next link:
`http://ec2-52-43-235-109.us-west-2.compute.amazonaws.com/index.php?r=consumer/index&token=5yKU4UIv9DQmO30LPW8ShMPkUXPAewrl`
`token` can be changed.
The code to manage data is located in `controllers/ConsumerController.php` file. 
Model for posted message is located in `models/TradeMessage.php`. 
All data should pass validation before saving. Yii framework validation functionality was used to validate data.
Client should receive JSON data in the next format to check that he/she send correct data: 
```
{
  "status": "eg. 200/500/404",
  "code": "error code",
  "message": "Error or success message"
}
```

###Message Processor - Average & Hard
After data are saved in DB we can process it and save it in DB in the way we like. 
To process data was decided to use `Observer` template. Two interfaces are located in `models/interfaces` folder.
`Observer` and `Processor` interfaces implements `Observer` template.
An implementation of `Observer` interface are located in `model/TradeObserver.php` file. `TradeObserver` used to notify NodeJS server that data was saved in redis. NodeJS server send data to the socket.io clients that are connected to the specific socket.
NodeJS server are located in `nodejs/server.js` file. It is configured to listen Redis channels (in my case `notification`) and communicate with `socket.io`.
`TradeRateProcessor` was created to process data and save average rate for currency pairs (eg. UAH/RUB). This data is used in frontend for chart.
Using `Processor` interface we can create more trade processors classes, for example: 
- TradeUserProcessor - to collect data grouped by user
- TradeCountryProcessor - to collect data grouped by country 
- etc.

###Message Frontend - Average & Hard
As said before socket.io is used for real-time graphics/maps.

**Average** 

Processed data for average rage of currency pairs (EUR/GBR) are displayed on the following link 
`http://ec2-52-43-235-109.us-west-2.compute.amazonaws.com/index.php?r=site/rate`. You also can go to this link clicking on 'Currency Pair Rates' link on top navigation panel.
 The result should looks like this:
 ![Currency Pair Rates](http://image.prntscr.com/image/3fcf62f68218408585df9605ee9badcf.png)
 
 **Hard**
 
 The global map with real-time data and table are located on the main page. 
 When somebody post a message to our Message Consumer url the world map should display the originating country from which data was sent. 
 For example, if we send data with `"originatingCounty": "CA"` then we should see that 'CA' is appear under `Canada` on the map and the message should appear in the messages table. Label should disappear in a second or after new data is arrived.
 Here is an example: 
 ![Real-time world map](http://image.prntscr.com/image/d7b69f67a75b45c6bde02cf099c34560.png)
 socket.io clint is located in `web/js/socket-client.js` file.

DIRECTORY STRUCTURE
-------------------

      assets/             contains assets definition
      config/             contains application configurations
      controllers/        contains Web controller classes
      models/             contains model classes and interfaces
      runtime/            contains files generated during runtime
      tests/              contains various tests for the basic application
      vendor/             contains dependent 3rd-party packages
      views/              contains view files for the Web application
      web/                contains the entry script and Web resources

REQUIREMENTS / TECHNOLOGIES
---------------------------
1. Apache v2.4([website](https://httpd.apache.org/))
2. The minimum requirement by this project template that your Web server supports PHP 5.4.0.([website](http://php.net))
3. Redis v3([website](http://redis.io/))
4. NodeJS v6([website](https://nodejs.org/en/))
5. Socket.io([website](http://socket.io/))
6. Composer([website](https://getcomposer.org/))
7. Codeception test framework([website](http://codeception.com/))
8. JQuery([website](https://jquery.com/))
9. Yii2 Framework([website](http://www.yiiframework.com/))
10. Bootstrap([website](http://getbootstrap.com/))

CONFIGURATION
-------------
**Technologies like NodeJS, Apache etc. should be installed and configured based on the technology specification. All configuration can be found on technologies websites**
##### Redis DB
Edit the file `config/web.php` with real data, for example:

```php
'redis' => [
    'class' => 'yii\redis\Connection',
    'hostname' => 'localhost',
    'port' => 6379,
    'database' => 0,
],
```
#####Tokens
Edit the file `config/params.php` with your own tokens. You can use current tokens:
```php
'tokens' => [
   '5yKU4UIv9DQmO30LPW8ShMPkUXPAewrl',
   'sgX6mvjmtOTN9ZSTTwZWN501IZeY95Ka',
   'f9jP4ub9K5JOj920S6jy30o88oqi1uz7',
],
```

TESTING
-------
**All project tests located in `tests` folder.**
`Codeception` test framework are used for testing the project.
#####UNIT TESTS
To run tests you should run `codecept run`(if codecept was installed globally) from `tests` folder.
#####JSON GENERATION AND POST
To generate and send `JSON` data to the server you can run `send_json.php` from `tests` folder. To run it use `php send_json.php` command.
