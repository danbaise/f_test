# simple skeleton
* 一个简单的骨架
* 供自己练习用
* config main.php web_root 应该配置为 /

# nginx add conf
    location / { 
        # Redirect everything that isn't a real file to index.php
        try_files $uri $uri/ /index.php?$args;
    } 

