### BitKwik Software.

## Overview

BitKwik is a Bitcoin powered bill payment and transfer application tailored for Zambian users.  
It enables customers to:

- Instantly transfer Bitcoin to mobile money wallets
- Use Bitcoin seamlessly in local commerce

---

## Requirements

- [XAMPP (Apache Server, PHP >= 8.2)](https://www.apachefriends.org/download.html)  
- [Composer](https://getcomposer.org/download)  
- [OpenNode](https://opennode.com/)

---

## Installation

Please follow these steps carefully:

```bash
1. git clone https://github.com/chandachewe10/BitKwik.git
2. composer update
3. cp .env.example .env
4. Set DB credentials and OpenNode keys in .env
5. php artisan key:generate
6. php artisan migrate
```

create a customer user on the terminal by running the following script and follow the prompts

```bash
php artisan make:filament-user
```

Start your Application 
```bash
php artisan serve
```
## Screenshots
<img width="1365" height="683" alt="bitkwik1" src="https://github.com/user-attachments/assets/f47764d0-6397-4f82-bb53-2e3767becc6c" />
<img width="1350" height="622" alt="bitkwik2" src="https://github.com/user-attachments/assets/f92138e6-4c95-463d-923d-955b209bdccc" />
<img width="1360" height="666" alt="bitkwik3" src="https://github.com/user-attachments/assets/2e24d2a1-e2dd-4b73-905f-3ac283cf65eb" />
<img width="1365" height="659" alt="bitkwik4" src="https://github.com/user-attachments/assets/4decf4a0-7b09-4865-9baf-f464e91fdbba" />
<img width="1366" height="693" alt="bitkwik5" src="https://github.com/user-attachments/assets/4c4f8f8f-99ed-4df3-ab19-531232add462" />



