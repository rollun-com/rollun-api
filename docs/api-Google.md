# Библиотека для работы с Google API

## Быстрый старт на примере Gmail API

### Установка
Для работы с **Gmail api**:  
- Выполните *Step 1: Turn on the Gmail API* со страницы: 
[GmailAPI quickstart](https://developers.google.com/gmail/api/quickstart/php "Gmail API PHP Quickstart ")  
- Файл `client_secret.json` сохраните в папку `resources/Api/Google/` (от корня проекта).  
- Запустите инсталлер (подробнее [тут](https://github.com/avz-cmf/zaboy-installer#Запуск-установщиков "Запуск инсталлера")).  
- Доступ к `Gmail api`:  

    use rollun\api\Api\Gmail\GmailClient;
    use rollun\api\Api\Google\Gmail\GoogleServiceGmail;

    $gmailClient = new GmailClient;
    $googleServiceGmail = new GoogleServiceGmail($gmailClient);
    print_r(googleServiceGmail->getProfile());


### Почему так просто?
Под каждый сервис `Google API` нужно создавать свой класс сервиса и инсталлера. Для `Gmail api` они уже есть в библиотеке. Но новые создать не сложно.   
Класс клиента наследуйте от `GoogleClientAbstract`. Вы можете (но не обязаны) переопределить:

    const CLIENT_SECRET_FULL_PATH = 'resources/Api/Google/client_secret.json';
    const CREDENTIALS_PATH = 'data/Api/Google/';
То есть Вы можете создать класс без кода. Просто укажите его в инсталлере для формирования ключа доступа.

Класс клиента наследуйте от `CredentialsInstallerAbstract`. Вы можете (но не обязаны) переопределить:

    const CLIENT_CLASS = false; // тут имя класса из прошлого шага
    const APPLICATION_NAME = 'Super APP'; // имя приложения из шага 1

    protected $scopes = array(Google_Service_Gmail::GMAIL_READONLY); // см. ниже

Массив  `protected $scopes` - список разрешений, запрашиваемых для Вашего приложения. Полный список для Gmail [тут](https://developers.google.com/gmail/api/auth/scopes "Полный список разрешений для Gmail").

### Я получил клиента для API - что дальше?
Для каждого сервиса Google API есть объект, который с ним работает. Он принимает клиента в параметре констрактора. Для Google Sheets:   
    `$service = new Google_Service_Sheets($client);`

### Как работать с **Gmail api**?
Для удобства работы с `Gmail api` в этой библиотеке есть объекты `MessagesList` и `Message`. 