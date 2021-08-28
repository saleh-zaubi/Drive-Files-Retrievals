# Google-Drive-Files-Retriever
Google Drive files retriever with OAuth 2.0 built using Yii2

# How to use
-Configure your database connection <br />
Run php yii migrate

-You need to add the following to frontend/services/google/files/credentials.json: <br />
client_id <br />
client_secret <br />
redirect_uris <br />
redirect uri must end be like: domain/index.php?r=drive/auth-return-url