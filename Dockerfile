FROM php:7.4-cli
COPY web /usr/src/myapp
WORKDIR /usr/src/myapp
CMD [ "cd", "/usr/src/myapp" ]
CMD [ "php", "-S", "0.0.0.0:8080" ]
