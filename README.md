# Spese Backend - Laravel Expense Tracker

Applicazione Laravel 11 per il tracciamento delle spese personali con riconoscimento intelligente delle transazioni.

## Deploy su Railway.app

### Passaggi per il deploy:

1. **Pusha il codice su GitHub**
   ```bash
   git add .
   git commit -m "Prepare for Railway deploy"
   git push origin main
   ```

2. **Connetti Railway a GitHub**
   - Vai su https://railway.app
   - Login con GitHub
   - Clicca "New Project" → "Deploy from GitHub repo"
   - Seleziona questo repository

3. **Aggiungi il database PostgreSQL**
   - Nel progetto Railway, clicca "+ New" → "Database" → "PostgreSQL"
   - Railway creerà automaticamente le variabili d'ambiente `DATABASE_URL`

4. **Configura le variabili d'ambiente**
   Aggiungi queste variabili in Railway (Settings → Variables):
   ```
   APP_NAME=Spese Backend
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://your-app.railway.app
   SESSION_DRIVER=cookie
   SESSION_SECURE_COOKIE=true
   CACHE_DRIVER=database
   QUEUE_CONNECTION=sync
   ```

5. **Deploy automatico**
   - Railway deployerà automaticamente ad ogni push su main
   - Il primo deploy può richiedere 2-3 minuti

6. **Esegui le migrazioni**
   Dopo il primo deploy, apri i Logs ed esegui:
   ```bash
   php artisan migrate --force
   php artisan db:seed --class=CategorySeeder
   php artisan key:generate --show
   ```
   
   Copia la APP_KEY generata e aggiungila alle variabili d'ambiente in Railway.

### Comandi utili

```bash
# Locale
php artisan serve
php artisan migrate
php artisan db:seed --class=CategorySeeder

# Production (via Railway CLI)
railway run php artisan migrate --force
railway run php artisan db:seed --class=CategorySeeder
```

## Sviluppo Locale

### Requisiti
- PHP 8.2+
- Composer
- SQLite o PostgreSQL

### Installazione

```bash
# Installa dipendenze
composer install

# Copia ambiente
cp .env.example .env

# Genera chiave app
php artisan key:generate

# Esegui migrazioni
php artisan migrate
php artisan db:seed --class=CategorySeeder

# Avvia server
php artisan serve
```

## Funzionalità

- **Gestione conti**: Crea e gestisci múltiples conti correnti
- **Spese**: Registra spese manualmente o con AI (riconoscimento da descrizione testuale)
- **Accrediti**: Registra entrate con riconoscimento AI
- **Categorie**: 8 categorie predefinite (Alimentari, Trasporti, Casa, Svago, Salute, Vestiti, Regali, Altro)
- **Riconoscimento AI**: L'AI estrae automaticamente importo e categoria dalla descrizione testuale

## Note

- OCR immagini rimosso per semplificare il deploy cloud
- Sessioni configurate con cookie per production
- Database PostgreSQL compatibile con Railway
