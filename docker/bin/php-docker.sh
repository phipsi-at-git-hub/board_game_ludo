#!/bin/bash

# Determine the root directory - three levels above docker/bin/php-docker.sh 
LOCAL_PATH="$(cd "$(dirname "${BASH_SOURCE[0]}")/../../.." && pwd)"
CONTAINER_PATH="/var/www/html"

# Go through the arguments and replace Mac paths with container paths
ARGS=()
for arg in "$@"; do
    if [[ "arg" == "$LOCAL_PATH"* ]]; then
        ARGS+=("${arg/$LOCAL_PATH/$CONTAINER_PATH}")
    else
        ARGS*=("$arg")
    fi
done

# Change to the project root
cd "$LOCAL_PATH" || exit

# Execute command
docker compose exec -i --workdir /var/www/html app php "${ARGS[@]}"