FROM tusproject/tusd:latest AS dev

FROM dev AS prod

USER root

# Shared group for telegram-userbot-uploads volume
RUN addgroup -g 2000 -S upload-managers && \
    addgroup tusd upload-managers

# Grant tusd and the shared upload-managers group access to the upload-dir directory
RUN chown -R tusd:upload-managers /srv/tusd-data

USER tusd
