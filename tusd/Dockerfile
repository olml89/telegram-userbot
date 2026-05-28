FROM alpine:latest AS init

RUN apk add --no-cache acl

COPY tusd/init.sh /init.sh

FROM tusproject/tusd:latest AS dev

FROM dev AS prod

USER root

# Add tusd to the shared upload-managers group for the telegram-userbot-uploads volume
ARG UPLOAD_MANAGERS_GID
RUN addgroup -S -g ${UPLOAD_MANAGERS_GID} upload-managers &&  \
    addgroup tusd upload-managers

# Pre-create tusd default upload-dir directory to avoid root-owned mount points
# Set ownership of the upload-dir to tusd:upload-managers
RUN mkdir -p /srv/tusd-data/data && \
    chown -R tusd:upload-managers /srv/tusd-data/data

USER tusd
