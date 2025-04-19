# Description of project
## Mars Rover Mission

### Your Task
You’re part of the team that explores Mars by sending remotely controlled vehicles to the surface of the planet. Develop software that translates the commands sent from Earth to instructions that are understood by the rover.

### Requirements
- You are given the initial starting point (x,y) of a rover and the direction (N,S,E,W) it is facing.
- The rover receives a collection of commands. (E.g.) FFRRFFFRL
- The rover can move forward (f).
- The rover can move left/right (l,r).
- Suppose we are on a really weird planet that is square. 200x200, for example :)
- Implement obstacle detection before each move to a new square. If a given
sequence of commands encounters an obstacle, the rover moves up to the last
possible point, aborts the sequence and reports the obstacle.

### Take into account
- Rovers are expensive; make sure the software works as expected.

<br>
<br>
<br>

# Documentation
## Stack
This is the stack you should need to run this project:
- PHP v8.3.6
- Laravel v12
- MySQL v9.3.0

<br>

## Installation
You need Docker to run the MariaDB database. Then run the command `docker compose up` or `docker compose up -d` to create a container with a database based on `docker-compose.yml` file.

On the server side, run `php artisan serve` command to start Laravel as a server.

<br>

## Configuration
On your `.env` file you must have these variables:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mars
DB_USERNAME=root
DB_PASSWORD=password
```

When you connect to the database, you should run migrations with the command `php artisan migrate`. Also execute, on the database, this script on folder `database/migrations/sql/procedures.sql`. This will create a procedure to reset values on entities that are needed.

<br>

## Steps
This project works completely in a API RESTful way. Those instructions must follow these steps in the same order.

1) /api/reset
    - This just truncates data on tables (rovers, reports and obstacles entity) to make a new start. Sets new coordinates for Rover and puts it "offline" mode. Also delete all records on the reports entity and generate a new list of obstacles at the obstacles entity.
2) /api/connect
    - "Connects" with Rover vehicle. It is not possible to send commands to Rover if there is "no connection".
3) /api/commands/{commandsList}
    - You are able to send commands to Rover on `{commandsList}`. Those commands are:
        - F: Moves Rover forward one square in the direction (N, E, S, W) it is aiming. If it does not find any obstacle.
        - L: Turn Rover 90 degrees to the left.
        - R: Turn Rover 90 degrees to the right.
    - Direction is where Rover is aiming. Might be North, East, South or West.
    - Only accepts those three commands and no more than ten (10) commands at a time. E.g., `/api/commands/FFRLL`. It accepts upper or lower case.
    - You can execute this command as much as you want to give orders to Rover.

<br>

## Reports
Rover warns when it finds an obstacle (or finds the end of the map) on its way and sends the information to the entity Reports on the database.

<br>

## Obstacles
When reset (/api/reset), it generates a list of obstacles randomly in the database (entity obstacles). This list is generated based on variables:

```
private $maxX = 200;
private $maxY = 200;
private $percentage = 40;
```

The higher the values you write, the longer then obstacle list will take to generate. Percentage is the amount of obstacles that will exists on area (maxX * maxY).

<br>

## Recommendations
Start with a small area to work with, like 5x5 and a percentage of 30. This will give you an idea of how this project works.
