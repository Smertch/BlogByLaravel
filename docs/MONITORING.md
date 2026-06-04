# Prometheus и Grafana в этом проекте

В [docker-compose.yml](../docker-compose.yml) добавлены сервисы мониторинга:

| Сервис        | Назначение                         | URL на хосте              |
|--------------|-------------------------------------|---------------------------|
| **Prometheus** | Сбор и хранение метрик (TSDB)   | http://localhost:1518     |
| **Grafana**    | Дашборды и визуализация         | http://localhost:1519     |
| **cAdvisor**   | Метрики Docker-контейнеров      | http://localhost:1520     |
| **Loki**       | Хранилище логов для Grafana     | http://localhost:1521     |
| **Promtail**   | Сбор логов (Docker + Laravel)   | без публичного порта      |

Образ cAdvisor в compose: `zcube/cadvisor` (Docker Hub). Официальный `gcr.io/cadvisor/cadvisor` иногда требует настроенный `gcloud` для pull — при необходимости можно вернуть его в [docker-compose.yml](../docker-compose.yml).

Учётные данные Grafana по умолчанию: **логин `admin`**, **пароль `admin`** (при первом входе Grafana может попросить сменить пароль — можно пропустить для локальной разработки).

## Запуск

Из корня репозитория:

```bash
docker compose build webserver
docker compose up -d
```

Только сервисы мониторинга:

```bash
docker compose up -d prometheus grafana cadvisor loki promtail
```

После смены [nginx.conf](../docker/nginx/nginx.conf) пересоберите образ **webserver**: `docker compose build webserver`.

Если `docker compose` не видит демона, см. [docker/README.md](../docker/README.md) — **Docker на Linux: нет сокета / демон не отвечает**.

---

## Экспортёры Prometheus (хост, БД, веб, пробы)

Конфигурация: [docker/prometheus/prometheus.yml](../docker/prometheus/prometheus.yml), модули blackbox: [docker/prometheus/blackbox.yml](../docker/prometheus/blackbox.yml).

| Сервис | Что собирает | Порт в сети compose |
|--------|----------------|---------------------|
| **node-exporter** | CPU, память, диск, сеть **хоста** (через маунт `/` → `/host`) | `:9100` |
| **mysqld-exporter** | MySQL (глобальный статус, InnoDB и т.д.) | `:9104` |
| **redis-exporter** | Redis | `:9121` |
| **nginx-exporter** | Парсит **stub_status** с `http://webserver/nginx-status` | `:9113` |
| **php-fpm-exporter** | Статус пула FPM по FastCGI `tcp://php-fpm:9000/status` | `:9253` |
| **blackbox-exporter** | HTTP-пробы на `webserver` и TCP на `mysql:3306`, `redis:6379` | `:9115` |

На хост порты экспортёров **не проброшены** — смотреть метрики удобно через Prometheus или Grafana.

**MySQL:** mysqld_exporter **0.16+** не читает `DATA_SOURCE_NAME`. В compose заданы `--mysqld.address`, `--mysqld.username` и переменная **`MYSQLD_EXPORTER_PASSWORD`** (должна совпадать с `MYSQL_ROOT_PASSWORD` у сервиса `mysql`). В продакшене — отдельный пользователь с правами `PROCESS`, `REPLICATION CLIENT`, `SELECT` и секрет вместо пароля в файле.

**Nginx:** в [nginx.conf](../docker/nginx/nginx.conf) добавлен `location = /nginx-status` (доступ только с приватных подсетей и localhost).

**PHP-FPM:** файл [zz-status.conf](../docker/php-fpm/zz-status.conf) задаёт `pm.status_path` и `ping.path` для пула `www`.

**Blackbox:** job `blackbox-http` проверяет Laravel **GET /up** (ожидается 2xx). Job `blackbox-http-app` — главная `/` с допустимыми кодами 200/301/302/401/403 (редирект на логин не считается ошибкой пробы). `blackbox-tcp` — доступность портов MySQL и Redis.

Примеры PromQL: `node_memory_MemAvailable_bytes`, `mysql_global_status_threads_connected`, `redis_connected_clients`, `nginx_connections_active`, `php_fpm_up`, `php_fpm_active_children`, `probe_success{job="blackbox-http"}`.

---

## Как пользоваться Prometheus

1. Откройте http://localhost:1518  
2. Вверху вкладка **Query** (или **Graph** в старых версиях).
3. В поле запроса введите PromQL, например:
   - `up` — видно, какие цели сбора метрик «живы» (`1` = ок).
   - `container_memory_usage_bytes` — память контейнеров (после того как cAdvisor начал отдавать метрики).

4. Вкладка **Status → Targets** — список `scrape`-целей и ошибки подключения.  
   Помимо `prometheus` и `cadvisor` должны быть **node-exporter**, **mysqld-exporter**, **redis-exporter**, **nginx-exporter**, **php-fpm-exporter**, **blackbox-***.

5. **Alerts** — правила алертинга (в этой базовой конфигурации не заданы; их можно добавить в `docker/prometheus/prometheus.yml` или отдельными файлами и подключить через `rule_files`).

Полезно знать:

- Интервал опроса в [prometheus.yml](../docker/prometheus/prometheus.yml): `scrape_interval: 15s`.
- Конфиг можно править на диске и перезагрузить без рестарта контейнера:  
  `curl -X POST http://localhost:1518/-/reload`  
  (включено флагом `--web.enable-lifecycle`).

---

## Как пользоваться Grafana

1. Откройте http://localhost:1519 и войдите как `admin` / `admin`.
2. Источники данных создаются автоматически ([datasources.yml](../docker/grafana/provisioning/datasources/datasources.yml)):
   - **Prometheus** → `http://prometheus:9090`
   - **Loki** → `http://loki:3100` (uid `loki-blog`)
3. Создайте дашборд: **Dashboards → New → New dashboard → Add visualization**.  
   Выберите datasource **Prometheus** или **Loki**, введите запрос, сохраните панель и дашборд.

### Логи и ошибки (Loki + Promtail)

**Promtail** читает:

- **stdout/stderr всех контейнеров** этого Docker (через сокет `docker.sock`) — метки `job=docker`, `container`, `compose_service`;
- файлы **`storage/logs/*.log`** на хосте (Laravel) — метка `job=laravel`.

**Готовый дашборд:** после `docker compose up -d loki promtail grafana` откройте **Dashboards → папка Blog → «Blog — ошибки в логах (Loki)»**.  
Там две панели: фильтр по «похоже на ошибку» в Docker-логах и уровни `ERROR` / `CRITICAL` / `EMERGENCY` в файле Laravel.

**Docker и MySQL (Prometheus):** в папке **Blog** также провиженятся **«Docker monitoring»** (cAdvisor; без зависимости от `image!=""` у импорта **193**), **«Blog — Docker (Prometheus / cAdvisor)»** и **«Blog — MySQL …»**. После добавления JSON перезапустите Grafana: `docker compose restart grafana`.

**Explore (Loki):** значок компаса → datasource **Loki**. Примеры LogQL:

```logql
{job="docker"} |~ "(?i)(error|exception|fatal|panic)"
```

```logql
{job="laravel"} |~ "(?i)(ERROR|CRITICAL)"
```

```logql
{compose_service="php-fpm"} |= "error"
```

Учтите: строки без слова «error» (например, только HTTP 500 в access-логе) в первую панель не попадут — расширьте regex или смотрите сырой поток `{job="docker"}` без фильтра.

Если **Loki пустой**, подождите ~1 минуту после старта, проверьте `docker compose logs promtail`, и что контейнеры проекта реально пишут в stdout (Laravel по умолчанию пишет в `storage/logs/laravel.log` — это отдельный поток `job=laravel`).

### Готовые дашборды

- **Explore** (значок компаса): быстрые запросы к Prometheus без сохранения дашборда.
- Импорт по ID **193** (*Docker monitoring*) у многих локальных cAdvisor **пустой** (в метриках нет `image`/`name`). Для этого репозитория используйте провижененный дашборд **«Docker monitoring»** в папке Blog или импортируйте другой шаблон с https://grafana.com/grafana/dashboards/?search=cadvisor.

---

## cAdvisor

Веб-интерфейс: http://localhost:1520 — таблица контейнеров, CPU, память, сеть.

На **Docker Desktop для macOS/Windows** метрики хоста и части Docker могут быть неполными из‑за виртуальной машины; на Linux обычно всё ближе к «реальному» хосту.

---

## Связь с приложением blog

В [prometheus.yml](../docker/prometheus/prometheus.yml) уже подключены nginx-exporter, php-fpm-exporter и blackbox-пробы к приложению. Дополнительно можно добавить HTTP-метрики из самого Laravel (отдельный endpoint `/metrics`), если понадобится.

---

## Остановка и данные

```bash
docker compose stop prometheus grafana cadvisor loki promtail node-exporter mysqld-exporter redis-exporter nginx-exporter php-fpm-exporter blackbox-exporter
```

Тома `blog_prometheus_data` и `blog_grafana_data` сохраняют историю метрик и настройки Grafana. Удалить полностью:

```bash
docker compose down -v
```

*(осторожно: флаг `-v` удалит и том MySQL `blog_mysql_data`.)*

---

## Ошибка подключения к Docker API

См. [docker/README.md](../docker/README.md) — раздел **Docker на Linux: нет сокета / демон не отвечает**.

---

## Безопасность

Пароль Grafana и отсутствие TLS подходят **только для локальной разработки**. Для продакшена используйте секреты, HTTPS, отдельные пароли и ограничение доступа по сети.
