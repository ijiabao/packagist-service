# Composer开发包自动更新服务 (for github)



* github 上的 services 已不再支持，因此托管在 github 仓库的composer包无法自动更新

  > **Note:**
  > [GitHub   Services are being deprecated](https://developer.github.com/changes/2018-04-25-github-services-deprecation/). Please contact your integrator for more
  > information on how to migrate or replace a service with
  > [webhooks](https://developer.github.com/webhooks/) or
  > [GitHub Apps](https://developer.github.com/apps/differences-between-apps/#about-github-apps/).

* 此为`webhook`解决方案，在托管的`github`项目里设置: `settings / Webhooks / add webhook` 进行添加钩子，在Push或自定义事件时，会 Post 一个目标 URL 执行操作，通过此 URL 进行更新

* 把本项目中的`packagist-service.php`放在你的网站上，保证可访问即可。例如：http://www.jurlu.com/packagist-service.php

* 修改`packagist-service.php`文件中的参数：

  ```php
  // 填入 github 项目 webhook 配置页面, 请随机产生 
  $secret = 'CCE46UUjsZVZYXNuMo7FPTEI4Ri91vP2';
  
  // 您在 packagist.org 中的 username 和 api_token
  $user = 'ijiabao';
  $token = 'CFGx1pioMOEX5fzAX8sw';
  ```

* 网站配置完毕，在`github`项目中配置`webhook`, 进行测试:

  ```bash
  # 假设网站url为: http://www.jurlu.com/packagist-service.php
  # 网站需支持php, curl, openssl
  # Packagist包地址即是您的composer项目首页，如：https://packagist.org/packages/ijiabao/laravel-dbdump
  
  # 填入
  # Payload URL:
  http://www.jurlu.com/packagist-service.php?pkg=你的packagist项目地址
  # Secret: 填入上述$secret值
  # Content type: application/json
  # event: Just the push event
  
  # 提交 Add webhook
  # 会显示测试结果
  ```

