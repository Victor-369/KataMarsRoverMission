CREATE DEFINER=`root`@`%` PROCEDURE `mars`.`setReset`(
	IN maxX INT,
	IN maxY INT,
	IN percentage INT
)
BEGIN
	DECLARE valueX, valueY, tempDirection, totalSquares, maxObstacles INT;
	DECLARE direction VARCHAR(1);
	DECLARE dateNow DATETIME;
	
	-- Auto commit: false
	START TRANSACTION;

	-- Default vaules
	IF maxX IS NULL THEN 
        SET maxX = 10;
    END IF;
	
    IF maxY IS NULL THEN
        SET maxY = 10;
    END IF;
    
    IF percentage IS NULL THEN
        SET percentage = 30;
    END IF;

    
	-- -------------------------------------------
	--      Clean records and set values        --
	-- -------------------------------------------
    truncate table rovers;
	truncate table reports;
	truncate table obstacles;

	set dateNow = NOW();
	
	
	-- -------------------------------------------
	-- Generate random point (x, y) for rover   --
	-- -------------------------------------------
	SELECT FLOOR(0 + (RAND() * (maxX + 1))) into valueX;
	SELECT FLOOR(0 + (RAND() * (maxY + 1))) into valueY;
	
	
	-- -------------------------------------------
	--   Generate random direction for rover    --
	-- -------------------------------------------
	SELECT FLOOR(0 + (RAND() * 4)) into tempDirection;
	
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

	insert into rovers(x, y, direction, isActive, created_at) values(valueX, valueY, direction, 0, dateNow);
	
	
	-- -------------------------------------------
	--       Generate random obstacles          --
	-- -------------------------------------------
	
	-- Total area
	set totalSquares = maxX * maxY; 
	
	-- Set max obstacles by percentage
	set maxObstacles = FLOOR(totalSquares * (percentage / 100));
	
	-- Do not check duplicates
	FOR i IN 1..maxObstacles DO
		SELECT FLOOR(0 + (RAND() * (maxX + 1))) into valueX;
		SELECT FLOOR(0 + (RAND() * (maxY + 1))) into valueY;
	
		INSERT INTO obstacles (x, y, created_at) VALUES (valueX, valueY, dateNow);
	END FOR;
	
	COMMIT;
END

-- ---------------------------------------------------------------------------------------------

