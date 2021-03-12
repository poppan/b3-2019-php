
/*
SELECT DISTINCT gender from MOCK_DATA; -- ca marche

-- AGGREGATS / GROUP BY
SELECT gender, COUNT(id) from MOCK_DATA group by gender;
SELECT gender, YEAR(birthdate_as_date), COUNT(id) from MOCK_DATA group by gender, YEAR(birthdate_as_date);
SELECT YEAR(birthdate_as_date), gender, COUNT(id) from MOCK_DATA group by YEAR(birthdate_as_date), gender;
SELECT YEAR(birthdate_as_date), gender, COUNT(id) from MOCK_DATA group by YEAR(birthdate_as_date), gender order by YEAR(birthdate_as_date) asc, gender asc ;

*/

SELECT name from genders; -- ca marche
SELECT users.gender_id, genders.name, COUNT(users.id) from users left join genders on gender_id=genders.id group by gender_id;
SELECT users.gender_id, genders.name, COUNT(users.id) from users left join genders on gender_id=genders.id group by gender_id;

-- jointure , group, tri
SELECT
    genders.name as gender,
    YEAR(users.birthdate_as_date) as birthyear,
    COUNT(users.id)
from users
         left join genders on gender_id=genders.id
group by
    gender, birthyear
order by
    birthyear asc, gender asc

-- WHERE SUR CONDITION JOINTE
SELECT
    genders.name as gender,
    YEAR(users.birthdate_as_date) as birthyear,
    COUNT(users.id)
from users
         left join genders on gender_id=genders.id
where
        users.first_name like "%y%" -- les users dont le prenom contient "Y"
  AND genders.name like "%y%" -- ET les genders qui contiennent un "Y"
group by
    gender, birthyear
order by
    birthyear asc, gender asc

-- HAVING SUR RESULTAT DU GROUP BY
SELECT
    genders.name as gender,
    YEAR(users.birthdate_as_date) as birthyear,
    COUNT(users.id)
from users
         left join genders on gender_id=genders.id
group by
    gender, birthyear
having -- le having s'applique au resultat du group by
       gender like "%y%" -- ici on peut utiliser les alias
order by
    birthyear asc, gender asc
