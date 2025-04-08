mongo:
    image: mongo:latest
    restart: unless-stopped
    ports:
      - 27017:27017
    environment:
      MONGO_INITDB_DATABASE: ${MONGO_INITDB_DATABASE}
      MONGO_INITDB_ROOT_USERNAME: ${MONGO_INITDB_ROOT_USERNAME}
      MONGO_INITDB_ROOT_PASSWORD: ${MONGO_INITDB_ROOT_PASSWORD}
      MONGODB_DB: ${MONGODB_DB}
      MONGODB_USER: ${MONGODB_USER}
      MONGODB_PASSWORD: ${MONGODB_PASSWORD}
    volumes:
      - ./mongo/init.sh:/docker-entrypoint-initdb.d/init.sh:ro