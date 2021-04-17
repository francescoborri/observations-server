# Observations REST Web service

This REST web service is the back-end layer for the enviromental data monitor, developed by:
- [Pietro Bigiarini](https://github.com/bigiarinip)
- [Francesco Borri](https://github.com/francescoborri)
- [Samuele Rosi](https://github.com/BtwSam)
- Gabriel Scantee
- [Filippo Sonnati](https://github.com/filipposonnati)

It is expected to be used with the [client web-app](https://github.com/Leofatto7/ClientRest).

## Getting started

### Prerequisites
- [PHP 7.x](https://www.php.net/downloads)
- [Composer](https://getcomposer.org/download/)
- [Symfony](https://symfony.com/download)
- [MySQL](https://dev.mysql.com/downloads/)

### Installation
Follow the steps below to install and run locally the web service.
* Clone this repository:

    ```sh
    git clone https://github.com/francescoborri/observations-server.git
    ```
    You can also use the [GitHub CLI](https://github.com/cli/cli):
    
     ```sh
     gh repo clone francescoborri/observations-server
     ```
* Move into the project directory and install the required dependecies:

  ```sh
  cd observations-server
  composer update
  ```
* Set your custom database URL as you prefer by adding the `DATABASE_URL` variable in a file named `.env.local`:

  ```sh
  touch .env.local
  echo "DATABASE_URL=mysql://user:password@host:port/observations_database?serverVersion=server-version" > .env.local
  ```
  Remember to replace `user`, `password`, `host` and `port` with your MySQL credentials.
  You also have to set the `server-version`: follow the [Symfony guide](https://symfony.com/doc/current/doctrine.html#configuring-the-database).
* Generate the database and its schema using [Doctrine](https://www.doctrine-project.org/):

  ```sh
  bin/console doctrine:database:create
  bin/console doctrine:schema:create
  ```
  or
  ```sh
  symfony console doctrine:database:create
  symfony console doctrine:schema:create
  ```
* Now you can finally run the web service with the Symfony built-in web server:
  
  ```sh
  symfony server:start
  ```
  
  or
  ```sh
  symfony serve
  ```

### Usage
This web service provides the standard methods required by the REST architecture:
- **Read**

  - You can get a list of observations (an observation is a set of measures in a speficic moment of the temperature and the humidity in three different places, A, B and EXT) by sending a GET request to the URL `https://host:port/observations/`.
  There also several available query parameters to filter the data:
    - `orderby`, which can be `datetime` or `id` and it's the field used to sort the observations;
    - `sort`, which can be `asc` or `desc` and tells to the web service whether to sort in an ascending or descending way;
    - `start` and `end`, both `yyyy-MM-dd HH:ii:ss` formatted date in order to restrict the period of the observations;
    - `day`, `month`, and `year`, which are all integers allowing you to grep only observations within a certain day, month or year.
  - If you want a single observation given its id, you can simply follow the URL `https://host:port/observation/id` and replace the id.
  - There is also a third option which allows you to find a specific observation gived its `datetime`, you can achieve it by sending a GET request to the URL `https://host:port/observation/` and attach a `datetime` query parameter with a `yyyy-MM-dd HH:ii:ss` formatted date.
- **Create**

  A POST request to `https://host:port/observation/new` allows you to create an observation, by including a JSON with the date of the new observation and its values attached to the request body. An example is given below:

  ```json
  {
    "datetime": "2000-01-21 00:00:00",
    "aTemp": "10.00",
    "aHum": 50,
    "bTemp": "20.00",
    "bHum": 25,
    "extTemp": "30.00",
    "extHum": 0
  }
  ```
- **Update data**

  If you want update an observation, you can send a PUT request to the URL `https://host:port/observation/id` and replace the id with the id of the desired observation, attaching to the body request a JSON with ONLY new temperature or humidity values: you CAN'T update the datetime of an observation. An example is given below:
  
  ```json
  {
    "aTemp": "10.00",
    "aHum": 50,
    "bTemp": "20.00",
    "extHum": 0
  }
  ```
- **Delete data**

  - To delete an observation you can simply send a DELETE request to the URL `https://host:port/observation/id`.
  - You can also delete an observation given its datetime, by sending a DELETE request `https://host:port/observation/` attaching a `datetime` query parameter with the `yyyy-MM-dd HH:ii:ss` formatted desired date.

### Learn more
You can learn more [here](https://www.postman.com/francescoborri/workspace/observations-rest-web-service/overview). Also, a demo version of the web service is hosted [here](https://francescoborri.ddns.net:8000/observation/list).
