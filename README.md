# Eureka4PHP
PHP版Eureka客户端，将PHP端实现的服务注册到Eureka服务中心<br />

## 服务注册
当PHP开发的微服务启动后，需要向Eureka服务中心发起注册请求<br />
在PHP端通过curl请求：http://youdomain/eureka-php.php?ac=reg

## 心跳检测
由Eureka客户端（即微服务提供者）发起心跳检测<br />
考虑到PHP端服务实现定时任务不够优雅，建议用Linux **Crontab**定时向Eureka发起心跳请求，执行频率：15s<br />
&#35; crontab -e
添加一行（eurekaCheck.sh位置改为您本机中的位置，同时此文件需要执行X权限）<br />
&#42; &#42; &#42; &#42; &#42; /root/eurekaCheck.sh  > /dev/null 2>&1

## 取消注册
当服务提供者关闭时，应向Eureka服务中心发起取消注册请求<br />
在PHP端通过curl请求：http://youdomain/eureka-php.php?ac=unreg


## 注意事项
1、若Eureka服务端开启了权限认证，此时客户端请求服务端eureka_server地址格式为：http://用户名:密码@Eureka服务器域名:端口/eureka/<br />
2、若已经使用Crontab来让客户端定时续租时还出现服务状态不能保存的情况，排查方法：<br />
* Linux终端执行 # tail -f /var/log/cron ，看看定时任务是否每隔几秒就执行一次；<br />
* 手动在终端执行 /usr/bin/curl http://youdomain/eureka-php.php?ac=heartbeat 看是否正常（可以在此PHP文件中debug一些信息，看是否能正常返回）；<br />


## 相关截图
![image](https://raw.githubusercontent.com/ah-guobing/Eureka4PHP/master/Resources/DingTalk20171216142601.png)
![image](https://raw.githubusercontent.com/ah-guobing/Eureka4PHP/master/Resources/DingTalk20171216143131.png)

## 问题反馈
若在接入时有不明白的，欢迎联系QQ：46926125
