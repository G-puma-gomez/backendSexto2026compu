# backendSexto2026compu

## Desarrollado por

Puma Gomez

## Publicar cambios

```powershell
git add .
git commit -m "describe los cambios"
git push
```

## Configuración del administrador

En local, configura `api/src/config/admin.credentials.php` a partir de
`api/src/config/admin.credentials.example.php`. El archivo con credenciales está
excluido de Git; guarda la contraseña como un hash generado con PHP
`password_hash($password, PASSWORD_DEFAULT)`, no como texto plano.

En Render, configura las variables de entorno `ADMIN_USER`,
`ADMIN_PASSWORD_HASH` y `ADMIN_TOKEN_SECRET` con los mismos valores de la
configuración del administrador. `ADMIN_PASSWORD_HASH` debe contener el hash de
la contraseña, y `ADMIN_TOKEN_SECRET` debe ser un secreto aleatorio y privado.
vhvk