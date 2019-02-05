FROM php:7.2-fpm

# Install modules.
RUN apt-get update && apt-get install -y \
	curl \
	vim

# Install PHP extensions.
RUN docker-php-ext-install mysqli

# Install REDIS extension
# RUN pecl install --onlyreqdeps --force redis \
# 	&& rm -rf /tmp/pear \
# 	&& docker-php-ext-enable redis

CMD ["php-fpm"]