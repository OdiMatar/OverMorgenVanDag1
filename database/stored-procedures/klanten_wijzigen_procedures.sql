DROP PROCEDURE IF EXISTS sp_klant_detail;
DROP PROCEDURE IF EXISTS sp_klant_email_bestaat;
DROP PROCEDURE IF EXISTS sp_klant_contact_bijwerken;

DELIMITER //

CREATE PROCEDURE sp_klant_detail(IN p_klant_id BIGINT UNSIGNED)
BEGIN
    SELECT
        k.Id AS klant_id,
        k.Voornaam AS voornaam,
        k.Tussenvoegsel AS tussenvoegsel,
        k.Achternaam AS achternaam,
        k.Relatienummer AS relatienummer,
        k.Bijzonderheden AS bijzonderheden,
        u.email AS account_email,
        c.Id AS contact_id,
        c.Straatnaam AS straatnaam,
        c.Huisnummer AS huisnummer,
        c.Toevoeging AS toevoeging,
        c.Postcode AS postcode,
        c.Plaats AS woonplaats,
        c.Mobiel AS mobiel,
        c.Email AS email,
        CONCAT(c.Straatnaam, ' ', c.Huisnummer, IFNULL(CONCAT(' ', c.Toevoeging), '')) AS adres
    FROM Klant AS k
    INNER JOIN users AS u ON u.id = k.UserId
    INNER JOIN KlantPerContact AS kpc ON kpc.KlantId = k.Id
    INNER JOIN Contact AS c ON c.Id = kpc.ContactId
    WHERE k.Id = p_klant_id
      AND k.IsActief = b'1'
      AND kpc.IsActief = b'1'
      AND c.IsActief = b'1'
    LIMIT 1;
END//

CREATE PROCEDURE sp_klant_email_bestaat(
    IN p_email VARCHAR(100),
    IN p_klant_id BIGINT UNSIGNED
)
BEGIN
    SELECT COUNT(*) AS aantal
    FROM Contact AS c
    INNER JOIN KlantPerContact AS kpc ON kpc.ContactId = c.Id
    WHERE LOWER(c.Email) = LOWER(p_email)
      AND kpc.KlantId <> p_klant_id
      AND c.IsActief = b'1'
      AND kpc.IsActief = b'1';
END//

CREATE PROCEDURE sp_klant_contact_bijwerken(
    IN p_klant_id BIGINT UNSIGNED,
    IN p_email VARCHAR(100),
    IN p_mobiel VARCHAR(20),
    IN p_straatnaam VARCHAR(50),
    IN p_huisnummer INT,
    IN p_toevoeging VARCHAR(10),
    IN p_postcode VARCHAR(10),
    IN p_woonplaats VARCHAR(50),
    IN p_bijzonderheden VARCHAR(255)
)
BEGIN
    UPDATE Contact AS c
    INNER JOIN KlantPerContact AS kpc ON kpc.ContactId = c.Id
    INNER JOIN Klant AS k ON k.Id = kpc.KlantId
    SET
        c.Email = p_email,
        c.Mobiel = p_mobiel,
        c.Straatnaam = p_straatnaam,
        c.Huisnummer = p_huisnummer,
        c.Toevoeging = NULLIF(p_toevoeging, ''),
        c.Postcode = REPLACE(UPPER(p_postcode), ' ', ''),
        c.Plaats = p_woonplaats,
        c.DatumGewijzigd = NOW(6),
        k.Bijzonderheden = NULLIF(p_bijzonderheden, ''),
        k.DatumGewijzigd = NOW(6)
    WHERE k.Id = p_klant_id
      AND k.IsActief = b'1'
      AND kpc.IsActief = b'1'
      AND c.IsActief = b'1';

    SELECT ROW_COUNT() AS aantal_gewijzigd;
END//

DELIMITER ;
