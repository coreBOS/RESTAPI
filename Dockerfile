FROM php:8.3.11-fpm-bookworm
LABEL org.opencontainers.image.description="coreBOS REST API"

SHELL ["/bin/bash", "-c"]

# Use the default UTF-8 language.
ENV LANG C.UTF-8
ENV TZ=Europe/Rome

RUN ln -snf /usr/share/zoneinfo/"$TZ" /etc/localtime && echo "$TZ" > /etc/timezone \
    && apt-get update \
    && apt-get -y upgrade \
    && apt-get -y install --no-install-recommends \
        apt-utils \
        libonig-dev \
    && docker-php-ext-install -j"$(nproc)" mbstring \
    && apt-get autoremove -y \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*
COPY app /app
EXPOSE 80

WORKDIR /app
CMD ["php", "-S", "0.0.0.0:80", "-t", "public/"]