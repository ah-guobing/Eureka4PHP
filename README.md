# Eureka4PHP
PHP版Eureka客户端，将PHP端实现的服务注册到Eureka服务中心<br />

# 客户端发起心跳请求
考虑到PHP端服务实现定时任务不够优雅，建议用Linux **Crontab**定时向Eureka发起心跳请求，执行频率：15s<br />
&#35; crontab -e
添加一行<br />
&#42; &#42; &#42; &#42; &#42; sleep 15; /usr/bin/curl http://youdomain/eureka-php.php?ac=heartbeat

# 相关截图
![image](https://raw.githubusercontent.com/ah-guobing/Eureka4PHP/master/Resources/DingTalk20171216142601.png)
![image](https://raw.githubusercontent.com/ah-guobing/Eureka4PHP/master/Resources/DingTalk20171216143131.png)
