DROP PROCEDURE IF EXISTS sp_behandelingen_opties;
DROP PROCEDURE IF EXISTS sp_behandelingen_overzicht;
DROP PROCEDURE IF EXISTS sp_behandeling_detail;
DROP PROCEDURE IF EXISTS sp_behandeling_producten;
DROP PROCEDURE IF EXISTS sp_behandeling_product_detail;
DROP PROCEDURE IF EXISTS sp_behandeling_product_prijs_bijwerken;

DELIMITER //

CREATE PROCEDURE sp_behandelingen_opties()
BEGIN
    -- Opties voor de filterlijst op het behandelingenoverzicht.
    SELECT Naam
    FROM Behandeling
    WHERE IsActief = b'1'
    ORDER BY Id;
END//

CREATE PROCEDURE sp_behandelingen_overzicht(IN p_naam VARCHAR(150))
BEGIN
    -- Inner joins tellen alleen actieve producten die aan een actieve behandeling gekoppeld zijn.
    SELECT
        Behandeling.Id,
        Behandeling.Naam,
        Behandeling.Omschrijving,
        Behandeling.Duurminuten,
        Behandeling.Prijs,
        COUNT(DISTINCT Product.Id) AS AantalProducten
    FROM Behandeling
    INNER JOIN BehandelingPerVoorraad
        ON BehandelingPerVoorraad.BehandelingId = Behandeling.Id
        AND BehandelingPerVoorraad.IsActief = b'1'
    INNER JOIN Voorraad
        ON Voorraad.Id = BehandelingPerVoorraad.VoorraadId
        AND Voorraad.IsActief = b'1'
    INNER JOIN Product
        ON Product.Id = Voorraad.ProductId
        AND Product.IsActief = b'1'
    INNER JOIN MedewerkerPerBehandeling
        ON MedewerkerPerBehandeling.BehandelingId = Behandeling.Id
        AND MedewerkerPerBehandeling.IsActief = b'1'
    WHERE Behandeling.IsActief = b'1'
      AND (p_naam IS NULL OR p_naam = '' OR p_naam = 'Alle behandelingen' OR Behandeling.Naam = p_naam)
    GROUP BY
        Behandeling.Id,
        Behandeling.Naam,
        Behandeling.Omschrijving,
        Behandeling.Duurminuten,
        Behandeling.Prijs
    ORDER BY Behandeling.Naam;
END//

CREATE PROCEDURE sp_behandeling_detail(IN p_behandeling_id BIGINT UNSIGNED)
BEGIN
    -- Basisgegevens van een actieve behandeling.
    SELECT *
    FROM Behandeling
    WHERE Id = p_behandeling_id
      AND IsActief = b'1'
    LIMIT 1;
END//

CREATE PROCEDURE sp_behandeling_producten(IN p_behandeling_id BIGINT UNSIGNED)
BEGIN
    -- Producten worden via de koppeltabel en voorraad met inner joins opgehaald.
    SELECT
        Product.Id,
        Product.Naam,
        Product.Merk,
        Product.Omschrijving,
        Product.EANcode,
        Product.VerkoopPrijs,
        Voorraad.AantalOpVoorraad
    FROM BehandelingPerVoorraad
    INNER JOIN Behandeling ON Behandeling.Id = BehandelingPerVoorraad.BehandelingId
    INNER JOIN Voorraad ON Voorraad.Id = BehandelingPerVoorraad.VoorraadId
    INNER JOIN Product ON Product.Id = Voorraad.ProductId
    WHERE BehandelingPerVoorraad.BehandelingId = p_behandeling_id
      AND Behandeling.IsActief = b'1'
      AND BehandelingPerVoorraad.IsActief = b'1'
      AND Voorraad.IsActief = b'1'
      AND Product.IsActief = b'1'
    ORDER BY Product.Id;
END//

CREATE PROCEDURE sp_behandeling_product_detail(
    IN p_behandeling_id BIGINT UNSIGNED,
    IN p_product_id BIGINT UNSIGNED
)
BEGIN
    -- Detailgegevens combineren product, voorraad en de laatst bekende actieve leverancier.
    SELECT
        Product.Id,
        Product.Naam,
        Product.Merk,
        Product.Omschrijving,
        Product.EANcode,
        Product.Houdbaarheidsdatum,
        Product.InkoopPrijs,
        Product.VerkoopPrijs,
        Product.Opmerking,
        Voorraad.AantalOpVoorraad,
        Leverancier.Naam AS LeverancierNaam,
        Leverancier.Postcode AS LeverancierPostcode,
        Leverancier.Plaats AS LeverancierPlaats,
        Leverancier.Email AS LeverancierEmail,
        Leverancier.Mobiel AS LeverancierMobiel
    FROM BehandelingPerVoorraad
    INNER JOIN Behandeling ON Behandeling.Id = BehandelingPerVoorraad.BehandelingId
    INNER JOIN Voorraad ON Voorraad.Id = BehandelingPerVoorraad.VoorraadId
    INNER JOIN Product ON Product.Id = Voorraad.ProductId
    INNER JOIN LeverancierOrder
        ON LeverancierOrder.ProductId = Product.Id
        AND LeverancierOrder.IsActief = b'1'
    INNER JOIN Leverancier
        ON Leverancier.Id = LeverancierOrder.LeverancierId
        AND Leverancier.IsActief = b'1'
    WHERE BehandelingPerVoorraad.BehandelingId = p_behandeling_id
      AND Product.Id = p_product_id
      AND Behandeling.IsActief = b'1'
      AND BehandelingPerVoorraad.IsActief = b'1'
      AND Voorraad.IsActief = b'1'
      AND Product.IsActief = b'1'
    ORDER BY LeverancierOrder.Id DESC
    LIMIT 1;
END//

CREATE PROCEDURE sp_behandeling_product_prijs_bijwerken(
    IN p_product_id BIGINT UNSIGNED,
    IN p_verkoopprijs DECIMAL(8,2)
)
BEGIN
    -- Alleen de verkoopprijs wordt aangepast; DatumGewijzigd toont wanneer dit gebeurde.
    UPDATE Product
    SET
        VerkoopPrijs = ROUND(p_verkoopprijs, 2),
        DatumGewijzigd = NOW(6)
    WHERE Id = p_product_id;
END//

DELIMITER ;
