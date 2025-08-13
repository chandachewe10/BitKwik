### BitKwacha Software.

## Requirements

- [Xampp (Apache Server, PHP >= 8.2)](https://www.apachefriends.org/download.html)
- [Composer](https://getcomposer.org/download)
- [LNbits X-Api-Key AND Admin-Key](https://lnbits.com/)



## How it works

This is a project from AfricaWorks and BTrust Hackathon. It is a Bitcoin powered bill payment app tailored for Zambian users. It allows customers to pay for bills such Zesco units, water bills, TV Subscription Etc using BitCoin, also customers can transfer their BitCoins to their Mobile Money wallets Instantly!

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
## Screenshots
<img width="1366" height="693" alt="bitkwik5" src="https://github.com/user-attachments/assets/68da3f77-134f-4f8e-902f-77ab48dc360d" />
<img width="1365" height="659" alt="bitkwik4" src="https://github.com/user-attachments/assets/c367943e-1c21-47ed-a0f7-78c348db4796" />
<img width="1360" height="666" alt="bitkwik3" src="https://github.com/user-attachments/assets/1a05c0b4-8db2-4a88-aa5a-0c748f60aa12" />
<img width="1350" height="622" alt="bitkwik2" src="https://github.com/user-attachments/assets/d3eaeeab-0c2e-4109-b482-d0f5422eacca" />
<img width="1365" height="683" alt="bitkwik1" src="https://github.com/user-attachments/assets/8f27b4f0-6996-4b81-82f3-46c2017a6f17" />

