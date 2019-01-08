# avphp
网站有域名,域名采用TSL加密,TSL 有https://freessl.org/申请的Let's Encrypt提供加密服务
网站平台有APACHE+PHP+MYSQL搭建.
APACHE+PHP+MYSQL是有XAMPP打包下载的.

数据库手动建立通过软件navicat premium.
数据库中要手动建立用户表,users,里面记录用户名和密码,登录时间,最后一次搜索图片的番号,以及最后一次看电影的名称.
数据库里的图片表,是用navicat premium导入execl文件,来导入数据库,excel文件自己网线爬虫下来,保存为excel文件,以及下载番号对应的图片.

Index.html进行用户登录,用户名和密码记录在数据库中,登录是检查数据库是否符合,不符合跳转页面到主页重新登录界面,成功后跳转到sdf.php页面. 登录页面有后台页面check.php进行处理. Check.php进行IP地址次数检查,在规定时间内最多可以登录的次数,有radis服务进行检查.如果符合IP地址规定次数内的要求,就进行数据库检查,是否用户名和密码正确,通过后系统设置一个cookie值,规定一个cookie值的有效时间,并且跳转到sdf.php页面.跳转的时候,页面带入一个根据用户名密码和时间的随机mh5的token值,这个用户名和token值作为参数带入下一个页面,并且每个页面都要检查这两个值,如果不符合,就退出网站到登录页面.
Sdf.php页面先进行ip次数和cookie,token值的检查.正确后,建立数据库,把用户IP,用户名,登录时间记录到数据库.然后页面显示3个随机的图片,图片来自数据库中定义的番号.每个图片带一个链接到下一个页面,get.php,带入参数:用户名,token值,以及一个要查询的番号itt.

Get.php页面进行cookie,token检查,检查参数itt,或者submit POST,tmp的内容,tmp说明内容来自页面sdf.php,根据itt或者submit,来查询数据库,并且显示

Allpic.php 用来显示一个番号下的所有图片,本地地址要求如bbanall/bban00222

Ipsum.php 查询一个时间内的在线总人数,以为每个用户的IP,和IP位置.最后一次看的电影.已经利用系统命令netstat查看连接状态.利用ajax,来查询IP地址的详细信息包括运行商(收费)送1000次查询,用到页面ippay.php.还有查询上传速度,用到页面getspeed.php,也是用到netstat命令




