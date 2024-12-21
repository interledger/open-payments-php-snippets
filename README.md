Open Payments Php Examples

```
console-app/
├── bin/
│   └── console
├── src/
│   ├── Command/
│   │   └── HelloCommand.php
│   │   └──
│   └── Application.php
├── tests/
│   └── ApplicationTest.php
├── vendor/
├── .env
├── composer.json
├── composer.lock
└── README.md
```

How to Run the Console Application

Install Dependencies:

```
composer install
```

Make the Console File Executable:

```
chmod +x bin/console
```


Run the Application:

```
./bin/console app:hello
```

