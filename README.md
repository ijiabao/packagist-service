# Composer开发包自动更新服务 (for github)



* github 上的 services 已不再支持，因此托管在 github 仓库的composer包无法自动更新

  > **Note:**
  > [GitHub   Services are being deprecated](https://developer.github.com/changes/2018-04-25-github-services-deprecation/). Please contact your integrator for more
  > information on how to migrate or replace a service with
  > [webhooks](https://developer.github.com/webhooks/) or
  > [GitHub Apps](https://developer.github.com/apps/differences-between-apps/#about-github-apps/).

* 新的自动更新方法

  > 参见: https://packagist.org/about#how-to-update-packages

  - 退出登录(github, packagist)

  - 登录packagist, 使用 github OAuth登录(第三方登录), 并开启所有权限

  - 进入profile页 https://packagist.org/profile/

  - 页面有显示 GitHub Hook Sync 和时间, 表示成功,以后github更新时,会自动推送到packagist.

    另可点击 "retry hook sync" 对你的github进行同步挂钩 (为github项目添加webhook). 

    ```
    GitHub Hook Sync
    
    Completed at 2018-10-17 02:51:29 UTC, retry hook sync.
    0 hooks setup/updated
    0 hooks already setup and left unchanged
    ```

  - 她的原理获得权限,在你的github项目里添加一个 webhook,  当PUSH代码时告之`packagist`进行更新

    可查看你的github项目=> settings => Webhooks , 如果有packagist.org/api/github, 则表示成功.

    ```
    Webhooks
    
    Webhooks allow external services to be notified when certain events happen. When the specified events happen, we’ll send a POST request to each of the URLs you provide. Learn more in our Webhooks Guide.
    
    https://packagist.org/api/github (push)
    ```

* 本文为webhook demo, 基于github api, 能实现同样效果,供参考

* 在托管的`github`项目里设置: `settings / Webhooks / add webhook` 进行添加钩子，在Push或自定义事件时，会 Post 一个目标 URL 执行操作，通过此 URL 进行更新

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

