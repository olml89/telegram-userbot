FROM tusproject/tusd:latest AS dev

FROM dev AS prod

USER root

# Shared group for telegram-userbot-uploads volume
RUN addgroup -g 2000 -S upload-managers && \
    addgroup tusd upload-managers

# Pre-create tusd default upload-dir directory to avoid root-owned mount points
# Set ownership of the upload-dir to tusd:upload-managers
# Enable the setgid bit in the upload-dir so files created inside inherit the parent's directory group ownership
RUN mkdir -p /srv/tusd-data/data && \
    chown -R tusd:upload-managers /srv/tusd-data/data && \
    chmod g+s /srv/tusd-data/data

USER tusd
