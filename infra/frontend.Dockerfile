FROM node:20-alpine
WORKDIR /app

# คัดลอกไฟล์ที่ต้องใช้ติดตั้งก่อน เพื่อให้ cache ได้
COPY package.json package-lock.json* ./
RUN npm ci

# คัดลอกซอร์สทั้งหมดของ frontend (ซึ่งคือ build context นี้)
COPY . .

# (ออปชัน) เปิดพอร์ต dev server
EXPOSE 5173

# คำสั่งรัน dev server
CMD ["npm","run","dev","--","--host","0.0.0.0"]
