# Description of project
## Mars Rover Mission

### Your Task
You’re part of the team that explores Mars by sending remotely controlled vehicles to the surface of the planet. Develop a software that translates the commands sent from earth to instructions that are understood by the rover.

### Requirements
- You are given the initial starting point (x,y) of a rover and the direction (N,S,E,W) it is facing.
- The rover receives a collection of commands. (E.g.) FFRRFFFRL
- The rover can move forward (f).
- The rover can move left/right (l,r).
- Suppose we are on a really weird planet that is square. 200x200 for example :)
- Implement obstacle detection before each move to a new square. If a given
sequence of commands encounters an obstacle, the rover moves up to the last
possible point, aborts the sequence and reports the obstacle.

### Take into account
- Rovers are expensive, make sure the software works as expected.

<br>
<br>
<br>

# Documentation
## Stack
This is the stack you should need to run this project:
- PHP v8.3.6
- Laravel v12
- MariaDB v11.4

<br>

## Installation
You need Docker to run MariaDB database. Use next command on same folder where you have `docker-compose.yml` file. Then run command `docker compose up` or `docker compose up -d` to create container with database.

On server side run `php artisan serve` command to start laravel as server.

<br>

## Configuration

On your `.env` file you must have this variables:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mars
DB_USERNAME=root
DB_PASSWORD=password
```

When you connect to database you should run migrations with command `php artisan migrate`. Also execute, on database, this script on folder `database/migrations/sql/procedures.sql`.

<br>

## Steps
This project works completely on API RESTful way. Those instructions must follow these steps.

1) /api/reset
    - This just truncate data on tables (rovers and reports) to make a new start.
2) /api/connect
    - "Connects" with Rover vehicle. It is not possible to send commands to Rover if there is no connection.
3) /api/commands/{commandsList}
    - You are able to send commands to Rover on `{commandsList}`. Those commands are:
        - F: Moves Rover forward one square on direction (N, E, S, W) it is aiming.
        - L: Turn Rover 90 degrees to left.
        - R: Turn Rover 90 degrees to right.
    - Direction is where Rover is aiming. Might be North, East, South or West.
    - Only accepts those three commands and no more than ten (10) commands at a time.

