# BCMS v4 - Installation (Localhost)

Target: Ubuntu Server 24.04 LTS, running on localhost (127.0.0.1)

## 1. Install prerequisites
```bash
sudo apt update && sudo apt -y upgrade
sudo apt -y install ca-certificates curl gnupg lsb-release git unzip
```

## 2. Install Docker + Compose
```bash
sudo install -m 0755 -d /etc/apt/keyrings
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /etc/apt/keyrings/docker.gpg
sudo chmod a+r /etc/apt/keyrings/docker.gpg

echo \
  "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/ubuntu \
  $(. /etc/os-release && echo $VERSION_CODENAME) stable" | \
  sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

sudo apt update
sudo apt -y install docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin
sudo usermod -aG docker $USER
newgrp docker
docker compose version
```

## 3. Setup project
```bash
cd ~/projects
mkdir -p bcms_v4 && cd bcms_v4
# copy-paste all repository files into this folder
cp .env.example .env
cp backend/.env.example backend/.env
cp frontend/.env.example frontend/.env
```

## 4. Run containers
```bash
docker compose up -d --build
```

## 5. Backend install + migrate + seed
```bash
docker compose exec backend composer install
docker compose exec backend php artisan key:generate
docker compose exec backend php artisan migrate:fresh --seed
```

## 6. Frontend install
```bash
docker compose exec frontend npm install
```

## 7. Access URLs
- Frontend: http://127.0.0.1:8080
- API health: http://127.0.0.1:8080/api/health
- Horizon: http://127.0.0.1:8080/horizon

## Default seed users (password = Password123!)
- abramz@maroon-net.id (Administrator)
- fandi@maroon-net.id (Supervisor)
- meci@maroon-net.id (Finance/Kasir)
- yogi@maroon-net.id (Support)

## 8. Troubleshooting
### If frontend not reachable
```bash
docker compose logs -f frontend
```

### If backend cannot connect to DB
```bash
docker compose logs -f backend postgres
```