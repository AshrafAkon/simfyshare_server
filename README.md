# SimfyShare

SimfyShare is a file sharing platform. It can be self hosted on any shared
hosting service. Installations steps are beginner friendly. You can use the online client
here: https://simfyshare.xyz
Online client and python client uses this repo without any change. So any file shared
with online client can be downloaded with python client and vice versa.
For python client please visit https://github.com/AshrafAkon/simfy_file_share

This server is created with basic security. It uses rest api to communicate
data between client and server. CLient application is responsible for
data encryption. Please note that `process_info_dict.php` logs user ip
and UserAgent to `info_dict_table` on mysql sever when "set_info_dict"
is set to "true"(its a string).

### Server setup steps

1. Upload the available files in this folder in your server.
2. Create a mysql database. then execute the following two commands
   to create required tables.

```
   CREATE TABLE info_dict_table ( id INT AUTO_INCREMENT PRIMARY KEY,
    file_basic_info BLOB, data_key VARCHAR(255) NOT NULL UNIQUE,
    info_dict_private_key VARCHAR(1024) NOT NULL, info_dict MEDIUMBLOB NOT NULL );
```

```
    CREATE TABLE time ( id_serial bigint(20) AUTO_INCREMENT PRIMARY KEY,
    id INT NOT NULL, file_name VARCHAR(255) NOT NULL UNIQUE,
    data_key VARCHAR(255) NOT NULL, time_uploaded TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP );
```

3. Create a folder outside webroot (so that its not publicly available
   through website.).
4. Edit `auth.php` with your configuration.

```
        $servername = url of your server
        $username = your mysql username
        $password = your mysql password
        $dbname = your mysql database name
        $main_upload_dir = full path of the folder created in step 3
        $security_key = choose a secure security key. make sure they both match on client side
```
