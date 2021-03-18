-- tous les users meme ceux qui n'ont pas d'addresse declarée dans la table de jointure
select * from users left join users_addresses ua on users.id = ua.user_id;

-- tous les users qui ont au moins une addresse declarée dans la table de jointure
select * from users right join users_addresses ua on users.id = ua.user_id;

-- tous les users ET le nom de leur adresse qui ont au moins une addresse declarée dans la table de jointure
select *
from users
         right join users_addresses ua on users.id = ua.user_id
         left join addresses a on ua.address_id = a.id

-- tous les users , leur genders ET le nom de leur adresse qui ont au moins une addresse declarée dans la table de jointure
select
    user_id,
    genders.name,
    first_name,
    last_name,
    city,
    country

from users
         left join genders on gender_id = genders.id
         right join users_addresses ua on users.id = ua.user_id
         left join addresses a on ua.address_id = a.id
