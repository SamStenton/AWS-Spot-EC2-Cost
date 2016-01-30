# AWS Spot EC2 Calculator

Before starting AWS Credentials needs to be filled in first in a `.env` file on the root. 
To see how much a Spot EC2 Server cost simply send a POST request to the applicaiton with the following inputs: 
- region (`eu-west-1`)
- availabilityZone (`eu-west-1c`)
- launchTime (`2016-01-25 12:35:00`)
- terminateTime (`2016-01-25 15:45:00`)
- instanceType (`m3.medium`)


### Official Lumen Documentation

Documentation for the Lumen framework can be found on the [Lumen website](http://lumen.laravel.com/docs).


### License

Both the Lumen Framework and this Repository are licensed under the [MIT license](http://opensource.org/licenses/MIT)
