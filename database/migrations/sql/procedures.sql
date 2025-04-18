CREATE DEFINER=`root`@`%` PROCEDURE `mars`.`setReset`(
	IN maxX INT,
	IN maxY INT
)
BEGIN
	DECLARE valueX, valueY, tempDirection INT;
	DECLARE direction VARCHAR(1);
	DECLARE dateNow DATETIME;
	
	-- Auto commit: false
	START TRANSACTION;

	

	-- -------------------------------------------
	--      Clean records and set values        --
	-- -------------------------------------------
    truncate table rovers;
	truncate table reports;

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
	
	COMMIT;
END

-- ---------------------------------------------------------------------------------------------

