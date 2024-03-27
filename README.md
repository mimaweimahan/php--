## 安装

### 基础环境

|软件|版本|备注|
|:----|:----|:----|
|Nginx|1.17.8|开启静态化|
|MySQL|5.6|    |
|PHP|Å7.2|不兼容5.6和8，会出问题|
|phpMyAdmin|4.7|可以不安装|
|Redis|5.0.8|必须安装|
|elasticsearch|7.*|必须安装|
|python|3|必须安装|

服务器 centos7.5

### PHP扩展：

|扩展|说明|备注|
|:----|:----|:----|
|fileinfo|通用扩展|若可用内存小于1G，可能会安装不上|
|opcache|缓存器|用于加速PHP脚本!|
|redis|缓存器|基于内存亦可持久化的Key-Value数据库|
|imagemagick|通用扩展|Imagick高性能图形库|
|imap|邮件服务|邮件服务器必备|
|exif|通用扩展|用于读取图片EXIF信息|
|intl|通用扩展|提供国际化支持|
|xsl|通用扩展|xsl解析扩展|

### python3扩展

|扩展|说明|备注|
|:----|:----|:----|
|redis|必装|必装|
|websocket-client|必装|必装|


### 注意事项和安装步骤

* 去除php禁用函数
* redis和elasticsearch需要先启动
* 根目录执行 composer update 安装 composer 依赖
* 服务期开放start.php使用的端口
* 修改.env文件中的数据库等配置信息后执行 php artisan config:cache
* 脚本任务 （tatabt.com为程序目录）
* 将数据库还原到配置文件指定的数据库中
* 项目依赖https，建议给socket做反向代理
#### 反向代理配置文件

location ~/(wss|socket.io) {

# 此处改为 socket.io 后端的 ip 和端口即可

proxy_pass[http://127.0.0.1:2000;](http://127.0.0.1:2020;)



proxy_set_header Upgrade $http_upgrade;

proxy_set_header Connection "upgrade";

proxy_http_version 1.1;

proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;

proxy_set_header Host $host;

}


#### 以下任务为定时重启项

cd /www/wwwroot/tatabt.com/public/vendor/webmsgsender

php start.php start

cd /www/wwwroot/tatabt.com/python

python3 main.py

cd /www/wwwroot/tatabt.com

php artisan websocket:client start

#### 以下任务为开机自启项


cd /www/wwwroot/tatabt.com

php artisan schedule:run

cd /www/wwwroot/tatabt.com

php artisan queue:work

cd /www/wwwroot/tatabt.com

php artisan auto_change start

cd /www/wwwroot/tatabt.com

php artisan get_market 2

cd /www/wwwroot/tatabt.com

php artisan robot 2

定时访问url 每三分钟派息一次
[https://tatabt.com/api/wealth/calc](https://www.coinsbas.com/api/wealth/calc)


### 

#### 以下计划任务，为平台币依赖项，可开机自启

cd /www/wwwroot/tatabt.com

php artisan get_kline_data_monthly

cd /www/wwwroot/tatabt.com

php artisan get_kline_data_weekly

cd /www/wwwroot/tatabt.com

php artisan get_kline_data_daily

cd /www/wwwroot/tatabt.com

php artisan get_kline_data_hourly



cd /www/wwwroot/tatabt.com

php artisan get_kline_data_thirtymin

### 
## 附：

### 安装es

rpm --import[https://artifacts.elastic.co/GPG-KEY-elasticsearch](https://artifacts.elastic.co/GPG-KEY-elasticsearch)

vi /etc/yum.repos.d/elasticsearch.repo

```plain
[elasticsearch-7.x]
name=Elasticsearch repository for 7.x packages
baseurl=https://artifacts.elastic.co/packages/7.x/yum
gpgcheck=1
gpgkey=https://artifacts.elastic.co/GPG-KEY-elasticsearch
enabled=1
autorefresh=1
type=rpm-md
```


yum install elasticsearch -y

### 安装python3和扩展

yum install python3 -y

pip3 install websocket-client redis

### 安装redis

yum install redis -y

### 安装nodejs

curl --silent --location[https://rpm.nodesource.com/setup_14.x](https://rpm.nodesource.com/setup_14.x)| bash -

yum install nodejs

