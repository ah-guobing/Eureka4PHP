# Eureka4PHP
PHP版Eureka客户端，将PHP端实现的服务注册到Eureka服务中心<br />

# 服务注册
当PHP开发的微服务启动后，需要向Eureka服务中心发起注册请求<br />
在PHP端通过curl请求：http://youdomain/eureka-php.php?ac=reg

# 心跳检测
由Eureka客户端（即微服务提供者）发起心跳检测<br />
考虑到PHP端服务实现定时任务不够优雅，建议用Linux **Crontab**定时向Eureka发起心跳请求，执行频率：15s<br />
&#35; crontab -e
添加一行<br />
&#42; &#42; &#42; &#42; &#42; sleep 15; /usr/bin/curl http://youdomain/eureka-php.php?ac=heartbeat

# 取消注册
当服务提供者关闭时，应向Eureka服务中心发起取消注册请求<br />
在PHP端通过curl请求：http://youdomain/eureka-php.php?ac=unreg

# 相关截图
![image](https://raw.githubusercontent.com/ah-guobing/Eureka4PHP/master/Resources/DingTalk20171216142601.png)
![image](https://raw.githubusercontent.com/ah-guobing/Eureka4PHP/master/Resources/DingTalk20171216143131.png)

# 问题反馈
若在接入时有不明白的，欢迎联系QQ：46926125
