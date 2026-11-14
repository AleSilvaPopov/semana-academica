# Semana Acadêmica de Computação da UFGD — Sistema Web

Sistema de gestão da Semana Acadêmica (Laravel 13 + MySQL 8) com duas áreas:
**Painel da Organização** (`/admin`) e **Portal do Participante** (`/`).

> Parte 1 — Estrutura da aplicação. O README completo é entregue na Parte 4.

## Instalação rápida

```bash
git clone <url-do-repositorio> semana-academica
cd semana-academica
composer install
npm install
cp .env.example .env          # ajuste DB_USERNAME / DB_PASSWORD
php artisan key:generate
php artisan storage:link
mysql -u root -p < database/sql/script.sql
npm run build
php artisan serve
```

Acesse http://localhost:8000 e http://localhost:8000/status (prova da conexão com o banco).

**Não rode `php artisan migrate`**: o banco é criado pelo `database/sql/script.sql`.
