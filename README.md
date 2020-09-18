# UK Postcode Finder
This is a Symfony 3.4 project which imports UK postcodes and their locations into a MySQL database.
You can search UK postcodes through API and you can also find postcodes near a specified location (latitude / longitude).
## Installation
**1.** Clone repository to your machine. Cd into your cloned folder.

**2.** After that, run to install composer dependencies:

`php -d memory_limit=-1 composer.phar update`


**3.** Make database connection configuration inside `app\config\parameters.yml` file.


**4.** Run to create database:

`php bin/console doctrine:database:create`


**5.** Run to update database schema:

`php bin/console doctrine:schema:update  --force`


**6.** Load sample user data:

`php bin/console doctrine:fixtures:load`


Sample user data is: `username=johndoe` `password=test` You will use this information for API authentication.

**7.** Download and import UK postcodes into database. (It may take some time, you can exit if some sample data is loaded):

`php bin/console app:downloadUkPostCodes`


This repository is using `LexikJWTAuthenticationBundle` bundle for API authentication.

You should create `public.pem` `private.pem` files inside `app\config\jwt\` for using `LexikJWTAuthenticationBundle` 

**8.** For creating `private.pem` file run:

`openssl genrsa -out app/config/jwt/private.pem -aes256 4096`


**9.** For creating `public.pem` run:

`openssl rsa -pubout -in app/config/jwt/private.pem -out app/config/jwt/public.pem`


**10.** Open a new terminal and start server for Symfony

`php bin/console server:run`

## Usage

Now you are ready to begin using the API endpoints.

**1.** Make a Post request  like below for getting a `Bearer` token for `johndoe`.

	curl -X POST -H "Content-Type: application/json" http://127.0.0.1:8000/api/login_check -d '{"username":"johndoe","password":"test"}'

If it works, you will receive something like this:

	{
	   "token" : "eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXUyJ9.eyJleHAiOjE0MzQ3Mjc1MzYsInVzZXJuYW1lIjoia29ybGVvbiIsImlhdCI6IjE0MzQ2NDExMzYifQ.nh0L_wuJy6ZKIQWh6OrW5hdLkviTs1_bau2GqYdDCB0Yqy_RplkFghsuqMpsFls8zKEErdX5TYCOR7muX0aQvQxGQ4mpBkvMDhJ4-pE4ct2obeMTr_s4X8nC00rBYPofrOONUOR4utbzvbd4d2xT_tj4TdR_0tsr91Y7VskCRFnoXAnNT-qQb7ci7HIBTbutb9zVStOFejrb4aLbr7Fl4byeIEYgp2Gd7gY"
	}



    
    


You must authenticate using `Bearer` token to make GET requests.

**2.** `For fetching postcodes with a partial string`, you should make a GET request to the API endpoint like below:

`http://127.0.0.1:8000/api/search_post_code?filter=Ab101 `


**3.** `For fetching postcodes near a specified location`, you should make a GET request to the API endpoint like below. `radius` parameter is representing kilometer (km). 

`http://127.0.0.1:8000/api/nearest?lat=57.149792&lon=-2.095293&radius=5`



