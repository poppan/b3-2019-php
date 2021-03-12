-- SQL ANSI 92
-- Structured Querying Language , American National Standard Institute, circa 1992

-- creation de la colonne "birthdate_as_date" de type datetime
ALTER TABLE MOCK_DATA ADD COLUMN 'birthdate_as_date' DATETIME null after 'birthdate';
-- insertion des valeurs de "birthdate" dans "birthdate_as_date"
UPDATE MOCK_DATA set birthdate_as_date = birthdate;

-- toutes les entrées
-- SELECT * from MOCK_DATA

-- tous les "steven"
SELECT * from MOCK_DATA where first_name = "Steven"; -- marche car match "EXACT"
SELECT * from MOCK_DATA where first_name = "steven"; -- marche aussi car la table est en collation case-insensitive (ci)

-- ceux qui "sonnent comme"
SELECT * from MOCK_DATA where SOUNDEX(first_name) = SOUNDEX('Brieuc');
SELECT * from MOCK_DATA where SOUNDEX(first_name) = SOUNDEX('Annabelle');

-- tous ceux qui commencent par "S", ne marche qu'avec les champs de type "String" text ou char / varchar
SELECT * from MOCK_DATA where first_name like "S%"; -- marche ceux qui commencent par "S"

-- nées avant le 1er janvier 1980
SELECT * from MOCK_DATA where birthdate < "1980-01-01";

-- nées en 1980 avec le champ birthdate (text)
SELECT * from MOCK_DATA where birthdate between "1980-01-01" AND "1980-12-31";
SELECT * from MOCK_DATA where birthdate like "1980%";
SELECT * from MOCK_DATA where EXTRACT( YEAR FROM birthdate) = 1980;
SELECT * from MOCK_DATA where YEAR(birthdate) = 1980;

SELECT * from MOCK_DATA where YEAR(birthdate) > 1980;
SELECT * from MOCK_DATA where YEAR(birthdate) >= 1980;

SELECT * from MOCK_DATA where YEAR(birthdate) < 1980;
SELECT * from MOCK_DATA where YEAR(birthdate) <= 1980;

SELECT * from MOCK_DATA where YEAR(birthdate) != 1980;
SELECT * from MOCK_DATA where YEAR(birthdate) <> 1980;

-- nées en 1980 avec le champ birthdate_as_date (datetime)
SELECT * from MOCK_DATA where birthdate_as_date between "1980-01-01" AND "1980-12-31";
SELECT * from MOCK_DATA where birthdate_as_date like "1980%";
SELECT * from MOCK_DATA where EXTRACT( YEAR FROM birthdate_as_date) = 1980;
SELECT * from MOCK_DATA where YEAR(birthdate_as_date) = 1980;


SELECT * from MOCK_DATA where
        YEAR(birthdate_as_date) = 1980
                          AND first_name like "S%";


SELECT CONCAT('TOTO', '.', 'PONEY'); -- concatenation => 'TOTO.PONEY'
SELECT CONCAT('TOTO', '.', 'PONEY', '.', 'YOLO', '.', 'CLUB'); -- concatenation => 'TOTO.PONEY.YOLO.CLUB'
SELECT CONCAT_WS('.', 'TOTO', 'PONEY', 'YOLO', 'CLUB'); -- concatenation => 'TOTO.PONEY.YOLO.CLUB'
SELECT CONCAT_WS(', ', 'TOTO', 'PONEY', 'YOLO', 'CLUB'); -- concatenation => 'TOTO, PONEY, YOLO, CLUB'
-- SELECT 'TOTO' + 'PONEY'; -- ne marche pas mais c'est censé etre pareil
/*
set @mavariable = 1980;
SELECT * from MOCK_DATA where YEAR(birthdate_as_date) = @mavariable;

set @mavariable = (SELECT MIN(YEAR(created)) from messages);
SELECT * from MOCK_DATA where YEAR(birthdate_as_date) = @mavariable
    UNION
SELECT * from MOCK_DATA where YEAR(birthdate_as_date) = @mavariable + 1
    UNION
SELECT * from MOCK_DATA where YEAR(birthdate_as_date) = @mavariable - 1
*/

SELECT MIN(birthdate_as_date) from MOCK_DATA; -- plus petite date presente
SELECT MAX(birthdate_as_date) from MOCK_DATA; -- plus grande date presente
SELECT COUNT(*) from MOCK_DATA; -- ca marche mais c'est pas le plus efficace
SELECT COUNT(id) from MOCK_DATA; -- ca marche mieux
SELECT COUNT(first_name) from MOCK_DATA; -- ca marche bof
SELECT COUNT(DISTINCT first_name) from MOCK_DATA; -- ca marche
SELECT COUNT(DISTINCT gender) from MOCK_DATA; -- ca marche
SELECT DISTINCT gender from MOCK_DATA; -- ca marche

-- AGGREGATS / GROUP BY
SELECT gender, COUNT(id) from MOCK_DATA group by gender;
SELECT gender, YEAR(birthdate_as_date), COUNT(id) from MOCK_DATA group by gender, YEAR(birthdate_as_date);
SELECT YEAR(birthdate_as_date), gender, COUNT(id) from MOCK_DATA group by YEAR(birthdate_as_date), gender;
SELECT YEAR(birthdate_as_date), gender, COUNT(id) from MOCK_DATA group by YEAR(birthdate_as_date), gender order by YEAR(birthdate_as_date) asc, gender asc ;


SELECT YEAR(birthdate_as_date) AS Année, gender as Genre, COUNT(id) as Nombre from MOCK_DATA group by Année, gender order by Année asc, gender asc ;




