#!/usr/bin/env bash
# =============================================================================
#  BEDO IoT Factory dashboard – install / update on the Raspberry Pi
#
#  Run from the extracted release folder:
#      tar -xzf bedo-factory-*.tar.gz
#      cd bedo-factory
#      sudo bash deploy/install-on-pi.sh
#
#  First run: installs nginx + PHP (needs internet ONCE), creates .env and
#  asks for the MQTT password of user "web". Later runs only update the code
#  and keep .env and storage/ (logs, sessions).
# =============================================================================
set -euo pipefail

APP_DIR=/var/www/bedo-factory
SITE=bedo-factory
SRC="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

if [[ $EUID -ne 0 ]]; then
    echo "Run with sudo:  sudo bash deploy/install-on-pi.sh" >&2
    exit 1
fi

echo "==> Source:  $SRC"
echo "==> Target:  $APP_DIR"

# --- 1. nginx + PHP ---------------------------------------------------------
need_pkgs=()
command -v nginx >/dev/null || need_pkgs+=(nginx)
command -v php   >/dev/null || need_pkgs+=(php-cli)
for ext in mbstring xml curl; do
    php -m 2>/dev/null | grep -qi "^${ext/xml/dom}$" || need_pkgs+=("php-$ext")
done
ls /run/php/php*-fpm.sock >/dev/null 2>&1 || need_pkgs+=(php-fpm)

if (( ${#need_pkgs[@]} )); then
    echo "==> Installing: ${need_pkgs[*]}  (needs internet)"
    apt-get update
    apt-get install -y "${need_pkgs[@]}"
fi

PHPV=$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')
if ! php -r 'exit(version_compare(PHP_VERSION, "8.2.0", ">=") ? 0 : 1);'; then
    echo "PHP $PHPV found – Laravel 12 needs PHP 8.2 or newer." >&2
    exit 1
fi
systemctl enable --now "php${PHPV}-fpm" >/dev/null
PHP_SOCK="/run/php/php${PHPV}-fpm.sock"
echo "==> PHP $PHPV  ($PHP_SOCK)"

# --- 2. Copy the app (keep .env and storage/ of an existing install) --------
mkdir -p "$APP_DIR"
if command -v rsync >/dev/null; then
    rsync -a --delete \
        --exclude '.env' --exclude 'storage/' --exclude 'bootstrap/cache/*.php' \
        "$SRC/" "$APP_DIR/"
else
    find "$APP_DIR" -mindepth 1 -maxdepth 1 ! -name .env ! -name storage -exec rm -rf {} +
    (cd "$SRC" && tar --exclude='./storage' --exclude='./.env' -cf - .) | (cd "$APP_DIR" && tar -xf -)
fi
# storage/ skeleton (only missing folders are created)
for d in app/public framework/cache/data framework/sessions framework/views logs; do
    mkdir -p "$APP_DIR/storage/$d"
done
mkdir -p "$APP_DIR/bootstrap/cache"

# --- 3. .env (first install only) -------------------------------------------
cd "$APP_DIR"
if [[ ! -f .env ]]; then
    echo "Type the password with the keyboard in ENGLISH."
    while true; do
        read -r -p "MQTT password for user 'web' (from mosquitto/setup_mosquitto.sh): " MQTT_PW
        set +e
        php artisan mqtt:check-login --host=127.0.0.1 --username=web --password="$MQTT_PW"
        rc=$?
        set -e
        [[ $rc -eq 0 ]] && break
        if [[ $rc -eq 2 ]]; then
            echo "Wrong password - try again."
            continue
        fi
        read -r -p "Mosquitto not reachable on this Pi. Save anyway? (y/n) " answer
        [[ $answer == y ]] && break
    done
    cp deploy/.env.production .env
    php artisan key:generate --force --no-interaction
    # escape for sed
    MQTT_PW_ESC=$(printf '%s' "$MQTT_PW" | sed -e 's/[\/&|]/\\&/g')
    sed -i "s|^MQTT_PASSWORD=.*|MQTT_PASSWORD=\"${MQTT_PW_ESC}\"|" .env
    echo "==> .env created (edit $APP_DIR/.env to change MQTT settings)"
fi

# --- 4. Permissions + Laravel caches -----------------------------------------
chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwX storage bootstrap/cache
chmod 640 .env && chown root:www-data .env

sudo -u www-data php artisan optimize:clear --no-interaction >/dev/null
sudo -u www-data php artisan optimize --no-interaction

# --- 5. nginx site -----------------------------------------------------------
sed -e "s|__APP_DIR__|$APP_DIR|g" -e "s|__PHP_SOCK__|$PHP_SOCK|g" \
    deploy/nginx-bedo-factory.conf > "/etc/nginx/sites-available/$SITE"
ln -sf "/etc/nginx/sites-available/$SITE" "/etc/nginx/sites-enabled/$SITE"
rm -f /etc/nginx/sites-enabled/default
nginx -t
systemctl enable --now nginx >/dev/null
systemctl reload nginx
systemctl restart "php${PHPV}-fpm"

IP=$(hostname -I | awk '{print $1}')
echo
echo "============================================================"
echo " Dashboard installed:  http://$IP/"
echo " Settings:             $APP_DIR/.env  (then: sudo bash $APP_DIR/deploy/install-on-pi.sh)"
echo "============================================================"
