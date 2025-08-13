### BitKwacha Software.

## Requirements

- [Xampp (Apache Server, PHP >= 8.2)](https://www.apachefriends.org/download.html)
- [Composer](https://getcomposer.org/download)
- [LNbits X-Api-Key AND Admin-Key](https://lnbits.com/)



## How it works

This is a project from AfricaWorks and BTrust Hackathon. It is a Bitcoin powered bill payment app tailored for Zambian users. It allows customers to pay for bills such Zesco units, water bills, TV Subsription Etc using BitCoin, also customers can transfer their BitCoins to their Mobile Money wallets Instantly!

## Installation

### Clone the repository
Please follow carefully the installation.

```bash
1. git clone https://github.com/chandachewe10/BitKwacha.git
2. composer update
3. copy .env.example .env 
4. Set DB Credentials and LNbits Keys in .env
4. php artisan key:generate
5. php artisan migrate 

```
 
create a customer user on the terminal by running the following script and follow the prompts

```bash
php artisan make:filament-user
```

Start your Application 
```bash
php artisan serve
```

