#!/bin/bash
APP_DIR="`dirname $PWD`/app" docker-compose -p gtd up -d --build --remove-orphans --force-recreate
