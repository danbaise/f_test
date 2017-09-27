# simple skeleton
* 一个简单的骨架
* 供自己练习用

# nginx add conf
    location / { 
        # Redirect everything that isn't a real file to index.php
        try_files $uri $uri/ /index.php?$args;
    } 