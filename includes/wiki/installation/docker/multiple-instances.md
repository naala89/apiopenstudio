Setting up with multiple instances
==================================

We will have multiple containers running separately:

* Database
* API
* Admin

These will run on the network:

* api_network

Create the network
------------------

    docker network create api_network

Start up the database
---------------------

    docker run -d \
    --network api_network --network-alias api_db \
    -v dbdata:/var/lib/mysql \
    -e MYSQL_ROOT_PASSWORD=apiopenstudio \
    -e MYSQL_USER=apiopenstudio \
    -e MYSQL_PASSWORD=apiopenstudio \
    -e MYSQL_DATABASE=apiopenstudio \
    mariadb:latest

validate it is running:

    docker exec -it <db-container-id> mysql -p
        (password apiopenstudio)
    show databases;

You should get:

    +--------------------+
    | Database           |
    +--------------------+
    | information_schema |
    | mysql              |
    | performance_schema |
    +--------------------+
    3 rows in set (0.001 sec)

Start up the API
----------------

