# SimfyShare

####SimfyShare is a file sharing platform. It can be self hosted on any shared
hosting service. Installations steps are beginner friendly.

There are two client application that uses this server. First one is the online client.
You can check a fully functional demo here: https://simfyshare.xyz. And other one is
a local Python client. Which has a basic gui built with tkinter. For python client
please visit https://github.com/AshrafAkon/simfy_file_share .
Online client and python client uses this repo as backend without any change. So any file shared
with online client can be downloaded with python client and vice versa.

This server is created with basic security. It uses rest api to communicate
data between client and server. CLient application is responsible for
data encryption. Please note that `process_info_dict.php` logs user ip
and UserAgent to `info_dict_table` on mysql sever when "set_info_dict"
is set to "true"(its a string).

### Server setup steps

1. To keep track of the uploads it uses MySql dtabase. First create a
   MySql database with phpmyadmin or any other native tools provided
   by your hosting. Then execute the following two commands in MySql
   console(It is norammlly located at bottom of phpmyadmin window).
   to create required tables.

```
   CREATE TABLE info_dict_table ( id INT AUTO_INCREMENT PRIMARY KEY,
   file_basic_info BLOB, data_key VARCHAR(255) NOT NULL UNIQUE,
   ip VARCHAR(40) NOT NULL, info_dict_private_key VARCHAR(1024) NOT NULL,
   info_dict MEDIUMBLOB NOT NULL, user_agent TEXT NOT NULL );
```

```
    CREATE TABLE time ( id_serial bigint(20) AUTO_INCREMENT PRIMARY KEY,
    id INT NOT NULL, file_name VARCHAR(255) NOT NULL UNIQUE,
    data_key VARCHAR(255) NOT NULL, time_uploaded TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP );
```

2. Create a folder outside webroot (this shouldnt be publicly available).
   This is where all the uploaded files will be stored.
3. Edit `auth.php` with your MySql configuration and upload folder directory.
   Security key parameter is important. It is used to validate request. every rest
   api call should send this key. Otherwise api will respond nothing.

```
        $servername = "" //url of your server
        $username = "" //your mysql username
        $password = "" //your mysql password
        $dbname = "" //your mysql database name
        $main_upload_dir = "" //full path of the folder created in step 3
        $security_key = "" //choose a secure security key. make sure they both match on client side
```

4. Upload all the files of this repo to your server. There is an `index.html` file.
   delete that file if you already have other index.html or index.php file.
