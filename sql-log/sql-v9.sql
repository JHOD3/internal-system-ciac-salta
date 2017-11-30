UPDATE usuarios SET superuser = 3 WHERE superuser = 2;
UPDATE usuarios SET superuser = 2 WHERE superuser = 1;
UPDATE usuarios SET superuser = 1 WHERE id_usuarios IN (13, 16);
