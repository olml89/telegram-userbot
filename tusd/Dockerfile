FROM tusproject/tusd:latest AS dev

FROM dev AS prod

# Create www-data, as it does not exist on tusproject/tusd:latest
RUN adduser -D -u 33 -s /sbin/nologin www-data
USER www-data
