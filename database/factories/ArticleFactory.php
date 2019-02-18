<?php

use Faker\Generator as Faker;

$factory->define(App\Article::class, function (Faker $faker) {
    return [
        'title'=>$faker->text(20),
        'body'=>$faker->text(100)
    ];
});
