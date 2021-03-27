# 一个不正经的图片库


### 使用说明

#### 1. clone 本项目

`git clone https://github.com/fengzi91/zhitiantu-api.git`

#### 2. 修改配置文件
`cp .env.example .env`

   mysql 配置
    
   ```
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
   ```

   全文搜索
   
   ```
SCOUT_DRIVER=meilisearch
MEILISEARCH_HOST=http://127.0.0.1:7700
MEILISEARCH_KEY=masterKey
  ```

   其他配置

   ```
APP_URL=http://pictures.test               // 接口域名
FRONT_URL=http://www.pictures.test:8080    // 前端访问域名 
SESSION_DOMAIN=.pictures.test              // session 域名
   ```

#### 3. 使用 sail 启动项目
    
   `sail up -d`
  
   关于 sail 参见文档：https://laravel.com/docs/8.x/sail
    
#### 4. 安装依赖

  `sail shell`
   
  `composer install`
      
#### 5. 导入测试数据

  使用数据库管理工具导入 `database/data.sql` 中的数据

#### 6. clone 前端项目
    
`git clone https://github.com/fengzi91/zhitiantu.git` 
   
#### 7. 运行项目
  `npm install && npm run dev`        
   
