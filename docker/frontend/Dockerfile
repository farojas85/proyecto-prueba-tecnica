FROM node:18-alpine

WORKDIR /app

# The frontend app code will be mounted as volume
# We install dependencies every time just in case, then run dev server
CMD sh -c "npm install && npm run dev"
