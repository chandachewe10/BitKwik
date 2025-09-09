### BitKwacha Software.

## Requirements

- [Xampp (Apache Server, PHP >= 8.2)](https://www.apachefriends.org/download.html)
- [Composer](https://getcomposer.org/download)
- [LNbits X-Api-Key AND Admin-Key](https://lnbits.com/)



## How it works

This is a project from BitDevs Zambia members during the Africa Free Routing Bootcamp's hackathon in Lusaka, Zambia. 
It is a Bitcoin powered bill payment app tailored for Zambian users. It allows customers to pay for bills such Zesco units, water bills, TV Subscription Etc using BitCoin, also customers can transfer their BitCoins to their Mobile Money wallets Instantly!

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
<img width="1365" height="683" alt="bitkwik1" src="https://github.com/user-attachments/assets/f47764d0-6397-4f82-bb53-2e3767becc6c" />
<img width="1350" height="622" alt="bitkwik2" src="https://github.com/user-attachments/assets/f92138e6-4c95-463d-923d-955b209bdccc" />
<img width="1360" height="666" alt="bitkwik3" src="https://github.com/user-attachments/assets/2e24d2a1-e2dd-4b73-905f-3ac283cf65eb" />
<img width="1365" height="659" alt="bitkwik4" src="https://github.com/user-attachments/assets/4decf4a0-7b09-4865-9baf-f464e91fdbba" />
<img width="1366" height="693" alt="bitkwik5" src="https://github.com/user-attachments/assets/4c4f8f8f-99ed-4df3-ab19-531232add462" />



