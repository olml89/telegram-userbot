FROM tusproject/tusd:latest AS dev

FROM dev AS prod

USER root

# We create a local www-data user/group in this container with fixed UID/GID 82 to match the backend container's
# www-data identity.
#
# This is required because Docker named volumes store file ownership as numeric UID/GID, not usernames.
# If tusd and backend use different UID mappings for www-data, files written by tusd would appear owned by a
#different user in backend, preventing backend from deleting or modifying uploaded files.
#
# This ensures both services treat the same files as owned by the same OS user (UID 82).
RUN addgroup -g 82 -S www-data || true
RUN adduser -S -D -H -u 82 -G www-data www-data

# Create /var/uploads as the expected mount point for the shared telegram-userbot-uploads Docker volume.
# This directory is used by tusd as the storage backend for uploaded files.
#
# Ownership is set to www-data (UID 82) to match the backend container owner, ensuring both services operate on the
# same files without permission conflicts.
#
# Note: when the Docker volume is mounted at runtime, it will override these
# permissions if it already exists. This step mainly affects first-time volume creation.
RUN mkdir -p /var/uploads && chown -R www-data:www-data /var/uploads

USER www-data
