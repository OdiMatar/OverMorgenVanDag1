DROP PROCEDURE IF EXISTS sp_producten_overzicht;
DROP PROCEDURE IF EXISTS sp_product_detail;
DROP PROCEDURE IF EXISTS sp_product_categorieen;
DROP PROCEDURE IF EXISTS sp_product_houdbaarheidsdatum_bijwerken;

DELIMITER //

CREATE PROCEDURE sp_producten_overzicht(IN p_categorie_id BIGINT UNSIGNED)
BEGIN
    SELECT
        Product.Id,
        Product.Naam,
        Product.Merk,
        Product.EANcode,
        Product.VerkoopPrijs,
        Categorie.Naam AS CategorieNaam,
        Voorraad.AantalOpVoorraad
    FROM Product
    INNER JOIN Categorie ON Product.CategorieId = Categorie.Id
    LEFT JOIN Voorraad ON Product.Id = Voorraad.ProductId
    WHERE Product.IsActief = b'1'
      AND (p_categorie_id IS NULL OR Product.CategorieId = p_categorie_id)
    ORDER BY Categorie.Naam, Product.Naam;
END//

CREATE PROCEDURE sp_product_detail(IN p_product_id BIGINT UNSIGNED)
BEGIN
    SELECT
        Product.Id,
        Product.Naam,
        Product.Omschrijving,
        Product.Merk,
        Product.EANcode,
        Product.Houdbaarheidsdatum,
        Product.InkoopPrijs,
        Product.VerkoopPrijs,
        Product.Opmerking,
        Categorie.Naam AS CategorieNaam,
        Voorraad.AantalOpVoorraad,
        Leverancier.Naam AS LeverancierNaam,
        Leverancier.Postcode AS LeverancierPostcode,
        Leverancier.Plaats AS LeverancierPlaats,
        Leverancier.Email AS LeverancierEmail,
        Leverancier.Mobiel AS LeverancierMobiel
    FROM Product
    INNER JOIN Categorie ON Product.CategorieId = Categorie.Id
    LEFT JOIN Voorraad ON Product.Id = Voorraad.ProductId
    LEFT JOIN (
        SELECT ProductId, MIN(Id) AS EersteLeverancierOrderId
        FROM LeverancierOrder
        GROUP BY ProductId
    ) AS EersteOrder ON Product.Id = EersteOrder.ProductId
    LEFT JOIN LeverancierOrder ON EersteOrder.EersteLeverancierOrderId = LeverancierOrder.Id
    LEFT JOIN Leverancier ON LeverancierOrder.LeverancierId = Leverancier.Id
    WHERE Product.Id = p_product_id
    LIMIT 1;
END//

CREATE PROCEDURE sp_product_categorieen()
BEGIN
    SELECT Id, Naam
    FROM Categorie
    WHERE IsActief = b'1'
    ORDER BY Naam;
END//

CREATE PROCEDURE sp_product_houdbaarheidsdatum_bijwerken(
    IN p_product_id BIGINT UNSIGNED,
    IN p_houdbaarheidsdatum DATE
)
BEGIN
    UPDATE Product
    SET
        Houdbaarheidsdatum = p_houdbaarheidsdatum,
        DatumGewijzigd = NOW(6)
    WHERE Id = p_product_id;
END//

DELIMITER ;
