FROM tusproject/tusd:latest AS dev

FROM dev AS prod

RUN adduser -D -u 33 -s /sbin/nologin www-data
USER www-data
