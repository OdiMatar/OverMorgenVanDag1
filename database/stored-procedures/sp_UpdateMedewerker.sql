DELIMITER //

DROP PROCEDURE IF EXISTS sp_UpdateMedewerker//

CREATE PROCEDURE sp_UpdateMedewerker(
    IN empId INT,
    IN vnaam VARCHAR(100),
    IN tvoeg VARCHAR(50),
    IN anaam VARCHAR(100),
    IN spec VARCHAR(100),
    IN gebdate DATE,
    IN opm VARCHAR(255),
    IN c_straat VARCHAR(150),
    IN c_huisnr VARCHAR(20),
    IN c_toev VARCHAR(20),
    IN c_pcode VARCHAR(10),
    IN c_plaats VARCHAR(100),
    IN c_email VARCHAR(255),
    IN c_mobiel VARCHAR(30)
)
BEGIN
    -- Update Medewerker
    UPDATE Medewerker
    SET Voornaam = vnaam,
        Tussenvoegsel = tvoeg,
        Achternaam = anaam,
        Specialisatie = spec,
        Geboortedatum = gebdate,
        Opmerking = opm,
        DatumGewijzigd = NOW()
    WHERE Id = empId;

    -- Update Contact (via MedewerkerPerContact)
    UPDATE Contact c
    JOIN MedewerkerPerContact mpc ON c.Id = mpc.ContactId
    SET c.Straatnaam = c_straat,
        c.Huisnummer = c_huisnr,
        c.Toevoeging = c_toev,
        c.Postcode = c_pcode,
        c.Plaats = c_plaats,
        c.Email = c_email,
        c.Mobiel = c_mobiel,
        c.Opmerking = opm,
        c.DatumGewijzigd = NOW()
    WHERE mpc.MedewerkerId = empId;
END//

DELIMITER ;
