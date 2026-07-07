DELIMITER //

DROP PROCEDURE IF EXISTS sp_GetMedewerkersBySpecialisatie//

CREATE PROCEDURE sp_GetMedewerkersBySpecialisatie(IN spec VARCHAR(100))
BEGIN
    IF spec = 'all' OR spec IS NULL OR spec = '' THEN
        SELECT m.*, c.Straatnaam, c.Huisnummer, c.Toevoeging, c.Postcode, c.Plaats, c.Email as ContactEmail, c.Mobiel
        FROM Medewerker m
        JOIN MedewerkerPerContact mpc ON m.Id = mpc.MedewerkerId
        JOIN Contact c ON mpc.ContactId = c.Id
        WHERE m.IsActief = 1;
    ELSE
        SELECT m.*, c.Straatnaam, c.Huisnummer, c.Toevoeging, c.Postcode, c.Plaats, c.Email as ContactEmail, c.Mobiel
        FROM Medewerker m
        JOIN MedewerkerPerContact mpc ON m.Id = mpc.MedewerkerId
        JOIN Contact c ON mpc.ContactId = c.Id
        WHERE m.Specialisatie = spec AND m.IsActief = 1;
    END IF;
END//

DELIMITER ;
