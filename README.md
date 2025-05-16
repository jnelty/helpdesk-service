# helpdesk-service

```bash
# Run helpdesk service
docker compose up

# Starting database migration after container is started
docker exec php84-fpm-service php bin/console doctrine:migrations:migrate

# Add fixtures data
docker exec php84-fpm-service php bin/console doctrine:fixtures:load --append
```
