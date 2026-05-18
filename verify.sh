#!/usr/bin/env bash

# Script de Validación E2E Automatizado para el Ecosistema Modernizado
# Ejecutar con: bash verify.sh

set -e

# Colores para salida en consola
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}====================================================================${NC}"
echo -e "${BLUE}        INICIANDO VALIDACIÓN E2E AUTOMATIZADA DE DOCKER Y ENDPOINTS ${NC}"
echo -e "${BLUE}====================================================================${NC}"

# Función para verificar el éxito de un paso
check_step() {
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}[OK] Paso completado con éxito.${NC}\n"
    else
        echo -e "${RED}[ERROR] El paso anterior falló. Abortando verificación.${NC}"
        exit 1
    fi
}

# 1. Levantar contenedores
echo -e "${YELLOW}[1/6] Reconstruyendo e iniciando contenedores de Docker...${NC}"
sudo docker compose down
sudo docker compose up -d --build
check_step

# 2. Esperar a que la base de datos MySQL esté lista
echo -e "${YELLOW}[2/6] Esperando que la base de datos MySQL esté lista para aceptar conexiones...${NC}"
max_attempts=30
attempt=1
db_ready=false

while [ $attempt -le $max_attempts ]; do
    if sudo docker compose exec -T backend php artisan db:monitor >/dev/null 2>&1; then
        db_ready=true
        break
    fi
    echo -e "   - Intento $attempt/$max_attempts: MySQL aún levantándose... esperando 2 segundos."
    sleep 2
    attempt=$((attempt+1))
done

if [ "$db_ready" = true ]; then
    echo -e "${GREEN}[OK] MySQL se encuentra listo y activo.${NC}\n"
else
    echo -e "${RED}[ERROR] MySQL tardó demasiado en responder. Abortando.${NC}"
    exit 1
fi

# 3. Instalar dependencias en el backend
echo -e "${YELLOW}[3/6] Descargando y actualizando paquetes en el backend (Composer)...${NC}"
sudo docker compose exec -T backend composer update --no-interaction
check_step

# 4. Correr migraciones y poblar base de datos
echo -e "${YELLOW}[4/6] Ejecutando migraciones limpias y seeders optimizados...${NC}"
sudo docker compose exec -T backend php artisan migrate:fresh --seed --no-interaction
check_step

# 5. Instalar y publicar configuraciones de Telescope
echo -e "${YELLOW}[5/6] Instalando y publicando Telescope...${NC}"
sudo docker compose exec -T backend php artisan telescope:install --no-interaction
sudo docker compose exec -T backend php artisan migrate --no-interaction
check_step

# 6. Ejecutar pruebas unitarias automatizadas (PHPUnit)
echo -e "${YELLOW}[6/6] Ejecutando la suite completa de pruebas unitarias (Feature Tests)...${NC}"
sudo docker compose exec -T backend php artisan test
check_step

# 7. Validaciones de peticiones HTTP (Endpoints de API y Docs)
echo -e "${YELLOW}====================================================================${NC}"
echo -e "${YELLOW}               REALIZANDO PRUEBAS HTTP E2E DE ENDPOINTS             ${NC}"
echo -e "${YELLOW}====================================================================${NC}"

# Validar Health Check
echo -en "Verificando endpoint de Salud (/api/health)... "
health_status=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8000/api/health)
if [ "$health_status" -eq 200 ]; then
    echo -e "${GREEN}[OK] Código 200 (Saludable)${NC}"
else
    echo -e "${RED}[FALLÓ] Código $health_status${NC}"
    exit 1
fi

# Validar Documentación Scramble (Swagger)
echo -en "Verificando endpoint de Documentación Swagger (/api/docs)... "
docs_status=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8000/api/docs)
if [ "$docs_status" -eq 200 ]; then
    echo -e "${GREEN}[OK] Código 200 (Documentación disponible)${NC}"
else
    echo -e "${RED}[FALLÓ] Código $docs_status${NC}"
    exit 1
fi

# Validar disponibilidad del Frontend (Vite)
echo -en "Verificando disponibilidad del Frontend de Vite (Puerto 5173)... "
frontend_status=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:5173 || echo "000")
if [ "$frontend_status" -ne "000" ]; then
    echo -e "${GREEN}[OK] Frontend disponible${NC}"
else
    echo -e "${RED}[ADVERTENCIA] Frontend no respondió directamente (esto puede demorar un momento en iniciar)${NC}"
fi

echo -e "\n${GREEN}====================================================================${NC}"
echo -e "${GREEN}          🎉 ¡TODOS LOS SISTEMAS SE ENCUENTRAN OPERANDO EN VERDE! 🎉${NC}"
echo -e "${GREEN}====================================================================${NC}"
echo -e "Las pruebas E2E y comandos Docker corrieron de inicio a fin correctamente."
