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

localhost:8000 へアクセス

# railway関連
```
// mysqlコマンド
mysql -h hopper.proxy.rlwy.net -P 26286 -u root -p

// マイグレーション
railway run bash -c "cd backend && php artisan migrate"
```