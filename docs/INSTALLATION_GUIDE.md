# BCMS v4 — Installation Guide (Localhost / Ubuntu Server)

## Prerequisites
- Ubuntu Server 22.04+ (recommended)
- Git
- Docker Engine + Docker Compose plugin

## 1) Install Docker
```bash
sudo apt update && sudo apt -y upgrade
sudo apt -y install ca-certificates curl gnupg git unzip
sudo install -m 0755 -d /etc/apt/keyrings
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /etc/apt/keyrings/docker.gpg
sudo chmod a+r /etc/apt/keyrings/docker.gpg

echo \
  "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/ubuntu \
  $(. /etc/os-release && echo "$VERSION_CODENAME") stable" | \
  sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

sudo apt update
sudo apt -y install docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin

sudo usermod -aG docker $USER
newgrp docker
```

## 2) Run the project
From repo root:
```bash
cp .env.example .env
docker compose up -d --build
```

## 3) Initialize Laravel (first time)
```bash
docker compose exec api bash -lc "cp .env.example .env && composer install && php artisan key:generate"
docker compose exec api bash -lc "php artisan migrate --seed"
```

## 4) Access URLs
- Nginx (entry): http://127.0.0.1/
- Frontend: http://127.0.0.1:3000
- API: http://127.0.0.1/api
- Horizon (via API path): http://127.0.0.1/api/horizon

## 5) Default seeded users
Password default semua user: `Password123!`
(Detail ada di seeder `DatabaseSeeder`.)

## Notes
- Untuk production: gunakan SSL/TLS (Let's Encrypt), pisahkan service DB/Redis, set env secure, enable HTTPS, rotate secrets.