<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Rover;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;



class RoverController extends Controller
{
    // Values to set max Mars area (squares)
    private $maxX = 5;
    private $maxY = 5;


    /*
    *   Function to reset all tables and rover's position, turning rover into "to waiting orders".
    */
    function setReset(): JsonResponse {
        // Clear all data and insert basic records
        $results = DB::select("CALL setReset($this->maxX, $this->maxY)");

        return response()->json([
            'status' => 'Ok',
            'message' => 'Values reset correctly',
        ], 200);
    }



    /*
    *   Function to move Rover.
    */
    function moveRover(Rover $rover): void {
        switch ($rover->direction) {
            // North
            case 'N':
                $rover->y += 1;
                break;

            // East
            case 'E':
                $rover->x += 1;
                break;

            // South
            case 'S':
                $rover->y -= 1;
                break;

            // West
            case 'W':
                $rover->x -= 1;
                break;
        }

        $rover->save();
    }




    /*
    *   Function to command Rover.
    */
    function commandsRover(Rover $rover, string $commands): int {
        $status = -1;

        // Convert commands into an array.
        $commandsArray = str_split(strtoupper($commands));

        for($i = 0; $i < count($commandsArray); $i++) { 
            switch($commandsArray[$i]) {
                // Forward
                case 'F':
                    $status = $this->isNextSquareFree($rover->x, $rover->y, $rover->direction);

                    if($status === 1) {
                        $this->moveRover($rover);
                    } else {
                        $report = new Report();
                        $report->x = $rover->x;
                        $report->y = $rover->y;

                        switch ($rover->direction) {
                            // North
                            case 'N':
                                $report->y = $rover->y + 1;
                                break;
                
                            // East
                            case 'E':
                                $report->x = $rover->x + 1;
                                break;
                
                            // South
                            case 'S':
                                $report->y = $rover->y - 1;
                                break;
                
                            // West
                            case 'W':
                                $report->x = $rover->x - 1;
                                break;
                        }

                        $report->status = 'Obstacle detected';
                        $report->save();
                    }
                    break;
            
                // Left
                case 'L':
                    $rover->direction = $this->newDirection($rover->direction, $commandsArray[$i]);
                    break;
                
                // Right
                case 'R':
                    $rover->direction = $this->newDirection($rover->direction, $commandsArray[$i]);
                    break;
            }
        }

        return $status;
    }





    /*
    *   Function to check next square.
    */
    function isNextSquareFree(int $roverX, int $roverY, string $direction): int {
        $isSquareFree = -1;

        switch ($direction) {
            // North
            case 'N':
                $roverY + 1 < $this->maxY ? $isSquareFree = 1 : $isSquareFree = 0;
                break;

            // East
            case 'E':
                $roverX + 1 < $this->maxX ? $isSquareFree = 1 : $isSquareFree = 0;
                break;

            // South
            case 'S':
                $roverY - 1 > 0 ? $isSquareFree = 1 : $isSquareFree = 0;
                break;

            // West
            case 'W':
                $roverX - 1 > 0 ? $isSquareFree = 1 : $isSquareFree = 0;
                break;
        }

        return $isSquareFree;
    }





    /*
    *   Function to get new direction of Rover.
    */
    function newDirection(string $actualDirection, string $command): string {
        $directions = [
            0 => 'N',
            1 => 'E',
            2 => 'S',
            3 => 'W',
        ];

        $index = array_search($actualDirection, $directions);

        switch($command) {
            case 'L':
                $result = $index - 1;

                if($result < 0) $result = 3;
                break;
        
            case 'R':
                $result = $index + 1;

                if($result > 3) $result = 0;
                break;
        }

        return $directions[$result];
    }




    /*
    *   Function to connect with rover and return coordinates and direction.
    */
    function setConnect(): JsonResponse {
        $rover = Rover::first();

        if(!$rover) {
            // When not possible to connect with Rover (no record on rover entity at database)
            $json = [
                'status' => 'Off line',
                'message' => "Not possible to connect with Rover",
            ];

            $status = 400;

            return response()->json($json, $status);
        }


        switch ($rover->isActive) {
            // When you connect to Rover for first time
            case 0:
                $rover->isActive = 1;
                $rover->save();

                $json = [
                    'status' => 'On line',
                    'message' => "Connected to Rover at point ($rover->x, $rover->y), direction $rover->direction",
                ];

                $status = 200;
                break;
            
            // When you connect to Rover when you already did it
            case 1:
                $json = [
                    'status' => 'On line',
                    'message' => "Already connected to Rover at point ($rover->x, $rover->y), direction $rover->direction",
                ];

                $status = 200;
                break;
        }

        return response()->json($json, $status);
    }





    /*
    *   Function to get commands and run them.
    */
    function setCommandsList(string $commands): JsonResponse {
        $rover = Rover::first();

        // When not possible to connect with Rover (no record on rover entity at database) or Rover is not active.
        if(!$rover || $rover->isActive === 0) {
            $json = [
                'status' => 'Off line',
                'message' => "Not possible to connect with Rover",
            ];

            $status = 400;

            return response()->json($json, $status);
        }


        // if not contains, at least, one letter (lrfLRF) then do not proceed with commands.
        if(!preg_match('/^[lrfLRF]+$/', $commands)) {
            $json = [
                'status' => 'Error',
                'message' => "Not possible to execute commands: There is one or more instructions that is not part of my parameters",
            ];

            $status = 400;

            return response()->json($json, $status);
        }


        // Rover do not accept more than 10 commands
        if(strlen($commands) > 10) {
            $json = [
                'status' => 'Error',
                'message' => "Rover do not accept more than ten (10) commands.",
            ];

            $status = 400;

            return response()->json($json, $status);
        }


        // Start to execute commands on Rover
        $result = $this->commandsRover($rover, $commands);


        switch ($result) {
            case 1:
                $json = [
                    'status' => 'Done',
                    'message' => 'Commands processed correctly',
                ];
        
                $status = 200;
                break;

            case 0:
                $json = [
                    'status' => 'Obstacle',
                    'message' => 'There is an obstacle which do not let me continue',
                ];
        
                $status = 200;
                break;
        }

        return response()->json($json, $status);
    }
}
