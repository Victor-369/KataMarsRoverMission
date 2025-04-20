DROP PROCEDURE IF EXISTS mars.setReset;

DELIMITER $$
$$
CREATE DEFINER=`usermars`@`%` PROCEDURE `mars`.`setReset`(
	IN maxX INT,
    IN maxY INT,
    IN percentage INT
)
BEGIN
	DECLARE valueX, valueY, tempDirection, totalSquares, maxObstacles, counter INT;
    DECLARE direction CHAR(1);
    DECLARE dateNow DATETIME;

    -- Auto commit: false
    START TRANSACTION;

    -- Default values and minimum values
    IF maxX IS NULL OR maxX < 2 THEN 
    	SET maxX = 2;
    END IF;
    
    IF maxY IS NULL OR maxY < 2 THEN
        SET maxY = 2;
    END IF;
    
    IF percentage IS NULL OR percentage < 2 THEN
        SET percentage = 1;
    END IF;

    
    
    -- -------------------------------------------
    --      Clean records and set values        --
    -- -------------------------------------------
    TRUNCATE TABLE rovers;
    TRUNCATE TABLE reports;
    TRUNCATE TABLE obstacles;

    SET dateNow = NOW();
    
    
    
    -- -------------------------------------------
    -- Generate random point (x, y) for rover   --
    -- -------------------------------------------
    SET valueX = FLOOR(0 + (RAND() * (maxX + 1)));
    SET valueY = FLOOR(0 + (RAND() * (maxY + 1)));
    
    
    
    -- -------------------------------------------
    --   Generate random direction for rover    --
    -- -------------------------------------------
    SET tempDirection = FLOOR(0 + (RAND() * 4));
    
    CASE tempDirection
        WHEN 0 THEN
            SET direction = 'N';
        WHEN 1 THEN
            SET direction = 'E';
        WHEN 2 THEN
            SET direction = 'S';
        WHEN 3 THEN
            SET direction = 'W';
    END CASE;

    INSERT INTO rovers(x, y, direction, isActive, created_at) VALUES(valueX, valueY, direction, 0, dateNow);
    
    
    
    -- -------------------------------------------
    --       Generate random obstacles          --
    -- -------------------------------------------
    SET totalSquares = maxX * maxY; 
    SET maxObstacles = FLOOR(totalSquares * (percentage / 100));
    
    IF maxObstacles > 0 THEN
	    SET counter = 1;
    
	    WHILE counter <= maxObstacles DO
	        SET valueX = FLOOR(0 + (RAND() * (maxX + 1)));
	        SET valueY = FLOOR(0 + (RAND() * (maxY + 1)));
	    
	        INSERT INTO obstacles (x, y, created_at) VALUES(valueX, valueY, dateNow);
	        
	        SET counter = counter + 1;
	    END WHILE;
	END IF;
    
    COMMIT;
END$$
DELIMITER ;
