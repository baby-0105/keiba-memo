# セットアップ
以下の手順に従ってセットアップを進めてください。
```
cd laravel-react-docker
cp backend/.env.example backend/.env
ln -s backend/.env .env
```

以下、任意の値を指定してください
```
DB_HOST=db
DB_PORT=3306
DB_ROOT_PASSWORD=[任意]
DB_DATABASE=[任意]
DB_USERNAME=[任意]
DB_PASSWORD=[任意]
```

```
docker-compose up -d
docker exec -it <appコンテナ名> composer install --no-dev --optimize-autoloader
docker exec -it <appコンテナ名> php artisan key:generate
```

dbで表示時の文字化け対策
```
docker exec -it db bash
cd /root
echo "[client]
default-character-set=utf8mb4" > .my.cnf
```

localhost:8000 へアクセス
