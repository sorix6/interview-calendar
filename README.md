## [ Interview Calendar Project ]



### OVERVIEW

Being able to schedule meetings while taking into account the availability of all the people expected to attend is a neccesity in most of our day to day lives. For internal usage within companies, a series of tools exist to help with this requirement. When it comes to scheduling interviews, this actions are more challenging as the people involved do not usually belong to the same organizations and most likely do not have access to each other's calendars through various tools.

### GOALS

The purpose of this project is to create a simple web API that allows people to exchange their availabilities and find convenient time slots to schedule meetings.

This application allows:

* The creation of accounts

    There are 2 types of accounts: Candidate and Interviewer but further development is needed in order to provide or restrict specific functionalities based on this type.

* Adding availabilities for an account 

* Visualize information about an account (including availability)

* Get the common available time slots for a series of accounts


### TECHNOLOGIES

This application is developed using PHP7.2 (with Slim) and data is persisted in a PostgreSQL database. 


The project can be run using Docker and the configuration files provided in the source code. The configuration sets up an Apache server, the PHP environment, an instance of Composer, a PostgreSQL database and an instance of Adminer (UI for database manipulation).

To run the application:

1. Using Docker
    * Your machine will need the following:
        * Docker
        * Docker compose
        * Composer

    * To setup the environment, go to the root of the project and run:
    ```
    docker-compose up
    ```

    The first time you run this command, the process might be slow as the images required will need to be downloaded on your computer. Once the build is done, you can start using the app.

    The application will be locally available at 127.0.0.1.
    The Adminer instance is available at 127.0.0.1:8080. The connexion details can be found in the file ```src/config/settings.php```

2. Without Docker
    * Your machine will need:
        * Access to a server
        * PHP7.2
        * Composer
        * A PostgreSQL database 
            * You will need to configure the database connexion in the application with your connexion information in ```src/config/settings.php```



### USING THE APPLICATION

The following end-points are available:

* **/account**

    ```Method: GET```

    It will return the complete list of accounts. You can access this end-point using your browser.

* **/account/{uuid}**

    ```Method: GET```

    It will return the account identified by the UUID and its list of availabilities. You can access this end-point using your browser.

* **/account**

    ```Method: POST```
    ``` Payload
        {
            "type":1
            "firstname":"jessie"
            "lastname":"Jon"
            "email":"jjon@yahoo.com"
        }
    ```
    The **type** parameter can be either 1 or 0, where 1 represents an Interviewer account.


    It will create a new account. For this call, you will need a tool such as Postman.

* **/account/uuid/{uuid}/availability**

    ```Method: POST```
    ``` Payload
        [
            {
                "date":"12-09-2019"
                "start":"9"
                "end":"12"
            },
            {
                "date":"12-09-2019"
                "start":"13"
                "end":"17"
            }
        ]
    ```
    You can add as many time slots as you require, for one or multiple dates.


    It will add the defined availabilities. For this call, you will need a tool such as Postman.  

* **/accounts/availability**

    ```Method: POST```
    ``` Payload
        {
            "account_uuid": [
                "3d869012-c8df-4f41-b866-4b813b38a4e8",
                "26f852c1-1b12-4b78-a6dd-3a69de7eaa4d",
                "a68d870c-4a06-4138-afaa-e0c77b64d230"
            ]
        }
    ```
    You can add as many accounts as you would need.

    It will return the time slots during which all accounts have availabilties. For this call, you will need a tool such as Postman.  

### FURTHER DEVELOPMENT

* The application does not, as of this time, support the suppression of either accounts or availabilities. 

* No authentication or authorization systems have been implemented. Separate account configuration for different types of account should be available.

* No unit tests have been yet added.

* Some parts of the code need refactoring to improve performance and the model.

Further development is required to address these issues.