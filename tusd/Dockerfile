FROM tusproject/tusd:latest AS dev

FROM dev AS prod

USER root

# Shared group for telegram-userbot-uploads volume
RUN addgroup -g 2000 -S upload-managers && \
    addgroup tusd upload-managers

# Pre-create tusd default upload-dir directory to avoid root-owned mount points
# Grant tusd and the shared upload-managers group access to the upload-dir
RUN mkdir -p /srv/tusd-data/data && \
    chown -R tusd:upload-managers /srv/tusd-data/data

USER tusd
