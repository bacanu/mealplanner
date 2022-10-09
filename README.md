# Mealplanner

A "scratch your own itch" tool that i've been using on and off for about 3 years.

## How to install locally in docker

Clone the project and cd into it

```
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/var/www/html \
    -w /var/www/html \
    laravelsail/php81-composer:latest \
    composer install --ignore-platform-reqs
```

### To start docker-compose

`export APP_PORT=8081 && vendor/bin/sail up`


### To enter the container:

`docker-compose exec -- laravel.test bash`

### To create a new laravel app

```
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/var/www/html \
    -w /var/www/html \
    laravelsail/php81-composer:latest \
    composer create-project laravel/laravel {name of app}
```

### To install create the docker-compose.yml

```
docker run --rm -it \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/var/www/html \
    -w /var/www/html \
    laravelsail/php81-composer:latest \
    php artisan sail:install
```



## How to use

0. On a desktop / laptop
1. Register an account and log in
2. Add some meals
3. List the ingredients for that meal and the quantity of each ingredient
4. From the meal list you can click [<<] to assign that meal to the first empty slot in the planner. Continue adding meals until you fill the list. Save
5. At the bottom of the planner you can find the list of ingredients for the **next** week. Use this as a shopping list

## Extra stuff

- For each ingredient you can add a price per unit. This allows for an estimation of the total cost of your upcoming shopping trip
- If you don't use the tool for a few weeks and want to copy the most recent planned week into the current week, you can do that by clicking the [Copy from last] button at the top of the page

