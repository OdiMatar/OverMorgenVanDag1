DELIMITER //

CREATE PROCEDURE sp_get_klanten_met_contactgegevens(IN p_postcode VARCHAR(10))
BEGIN
  SELECT
    k.Id AS klant_id,
    k.Voornaam AS voornaam,
    k.Tussenvoegsel AS tussenvoegsel,
    k.Achternaam AS achternaam,
    k.Relatienummer AS relatienummer,
    CONCAT(c.Straatnaam, ' ', c.Huisnummer, IFNULL(CONCAT(' ', c.Toevoeging), '')) AS adres,
    c.Postcode AS postcode,
    c.Plaats AS woonplaats,
    c.Mobiel AS mobiel,
    c.Email AS email
  FROM Klant AS k
  INNER JOIN KlantPerContact AS kpc ON kpc.KlantId = k.Id
  INNER JOIN Contact AS c ON c.Id = kpc.ContactId
  WHERE k.IsActief = b'1'
    AND kpc.IsActief = b'1'
    AND c.IsActief = b'1'
    AND (p_postcode IS NULL OR p_postcode = '' OR REPLACE(UPPER(c.Postcode), ' ', '') = REPLACE(UPPER(p_postcode), ' ', ''))
  ORDER BY k.Achternaam, k.Voornaam;
END //

CREATE PROCEDURE sp_log_technische_melding(
  IN p_onderdeel VARCHAR(100),
  IN p_melding VARCHAR(255),
  IN p_context JSON
)
BEGIN
  INSERT INTO TechnicalLog (Onderdeel, Melding, Context)
  VALUES (p_onderdeel, p_melding, p_context);
END //

DELIMITER ;
