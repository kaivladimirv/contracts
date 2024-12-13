#!/usr/bin/env bash

chmod 777 -R /app/storages/madeline/session.madeline

exec /usr/bin/supervisord -c /etc/supervisor/supervisord.conf
