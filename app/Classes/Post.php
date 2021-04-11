<?php

namespace App\Classes;

class Post
{
    public function getPosts()
    {
        $superMatrice = new Supermetrics();

        return $superMatrice->fetchPosts();
    }
}