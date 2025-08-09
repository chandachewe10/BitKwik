### BitKwacha Software.

## Requirements

- Xampp (Apache, PHP >= 8)


## How it works

This is a project from AfricaWorks and BTrust Hackathon. It is a Bitcoin powered bill payment app tailored for Zambian users.





## Installation

### Clone the repository
Please follow carefully the installation.

```bash
1. git clone https://github.com/chandachewe10/BitKwacha.git
2. composer install
3. copy .env.example .env and set DB Credentials
4. php artisan key:generate
5. php artisan migrate 

```
 
create a super-admin user on the terminal by running the following script and follow the prompts

```bash
php artisan make:filament-user
```

Start your Application 
