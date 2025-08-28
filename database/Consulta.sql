SELECT *
FROM assessments
WHERE DATE(date_time) = CURDATE();

SELECT *
FROM assessments
WHERE date_time BETWEEN '2024-04-01 00:00:00' AND '2024-04-30 23:59:59';

SELECT 
    *,
    LAG(date_time) OVER (PARTITION BY id_customers ORDER BY date_time) AS previous_assessment_date
FROM assessments;

DELIMITER //
CREATE TRIGGER assessment_hgt_check BEFORE INSERT ON assessment_hgt
FOR EACH ROW 
BEGIN 
   DECLARE message VARCHAR(255);
   
   IF NEW.grip_strength_values < 43 THEN 
      SET message = 'It is below the reference values';
   ELSEIF NEW.grip_strength_values >= 43 AND NEW.grip_strength_values <= 45 THEN 
      SET message = 'It falls in the reference values';
   ELSE 
      SET message = 'It is above reference values';
   END IF;
   
   SET NEW.considerations = message;
END;
//
DELIMITER ;

DELIMITER //
CREATE TRIGGER physiologist_limit_assignments
BEFORE INSERT ON assignments
FOR EACH ROW
BEGIN
    DECLARE counter INT DEFAULT 0;

    IF NEW.id_physiologist IS NOT NULL THEN
        SELECT COUNT(*) INTO counter
        FROM assignments
        WHERE id_physiologist = NEW.id_physiologist;

        IF counter >= 2 THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'This physiotherapist has reached the maximum number of clients.';
        END IF;
    END IF;
END;
//
DELIMITER ;

DELIMITER //

CREATE TRIGGER workout_restrictions
BEFORE INSERT ON workouts 
FOR EACH ROW 
BEGIN 
    DECLARE workout_count INT DEFAULT 0;
    DECLARE error_signaled BOOLEAN DEFAULT FALSE;  
    
    SELECT COUNT(*) INTO workout_count 
    FROM workouts 
    WHERE id_customers = NEW.id_customers
    AND WEEK(NEW.date_time, 1) = WEEK(date_time, 1)
    AND YEAR(NEW.date_time) = YEAR(date_time);
    
    IF workout_count >= 3 THEN 
        SET error_signaled = TRUE; -- Marca que um erro foi sinalizado
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'The client has reached the weekly training limit.';
    END IF;
    
    IF NEW.period_minutes > 30 AND NOT error_signaled THEN 
        SIGNAL SQLSTATE '45001' SET MESSAGE_TEXT = 'The client cannot train for more than 30 minutes.';
    END IF;
END;
//
DELIMITER ;
