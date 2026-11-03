FROM node:24-alpine AS dev
WORKDIR /app

# Production: build the Nuxt app once, then run the self-contained Nitro server (.output/).
FROM node:24-alpine AS build
WORKDIR /app
COPY frontend/ ./
RUN npm ci && npm run build

FROM node:24-alpine AS prod
WORKDIR /app
ENV NODE_ENV=production \
    HOST=0.0.0.0 \
    PORT=3000
COPY --from=build /app/.output ./.output
USER node
EXPOSE 3000
CMD ["node", ".output/server/index.mjs"]
