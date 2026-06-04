FROM nginx:alpine
# Bake nginx config into the image (avoids bind-mount config issues on some Docker setups)
COPY nginx.conf /etc/nginx/conf.d/default.conf
