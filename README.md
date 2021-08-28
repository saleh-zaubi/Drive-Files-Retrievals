# Google-Drive-Files-Retriever
Google Drive files retriever with OAuth 2.0 built using Yii2

# How to use
You need to add the following to frontend/services/google/files/credentials.json: <br />
client_id <br />
client_secret <br />
redirect_uris <br />
redirect uri must end be like: domain/index.php?r=drive/auth-return-url

Then you can go to /drive/files-list url