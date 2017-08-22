<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function index(){
        $data = [
            'carousel' => [
              ['img' => 'img/1.jpg', 'caption' => 'Описание 1'],
              ['img' => 'img/2.jpg', 'caption' => 'Описание 2'],
              ['img' => 'img/3.jpg', 'caption' => 'Описание 3'],
            ],
            'about' => [
                ['icon' => 'fa fa-american-sign-language-interpreting', 'title' => 'Преимущество 1', 'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. At, ex facere fugiat maxime molestiae non nostrum odio provident quibusdam quo, sint soluta ullam velit veritatis vitae. Dolor molestias nesciunt quasi!', 'link' => '#'],
                ['icon' => 'fa fa-american-sign-language-interpreting', 'title' => 'Преимущество 2', 'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. At, ex facere fugiat maxime molestiae non nostrum odio provident quibusdam quo, sint soluta ullam velit veritatis vitae. Dolor molestias nesciunt quasi!', 'link' => '#'],
                ['icon' => 'fa fa-american-sign-language-interpreting', 'title' => 'Преимущество 3', 'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. At, ex facere fugiat maxime molestiae non nostrum odio provident quibusdam quo, sint soluta ullam velit veritatis vitae. Dolor molestias nesciunt quasi!', 'link' => '#'],
                ['icon' => 'fa fa-american-sign-language-interpreting', 'title' => 'Преимущество 4', 'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. At, ex facere fugiat maxime molestiae non nostrum odio provident quibusdam quo, sint soluta ullam velit veritatis vitae. Dolor molestias nesciunt quasi!', 'link' => '#'],
            ],
            'rate' => [
                ['title' => 'Тариф 1', 'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. At, ex facere fugiat maxime molestiae non nostrum o'],
                ['title' => 'Тариф 2', 'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. At, ex facere fugiat maxime molestiae non nostrum o'],
                ['title' => 'Тариф 3', 'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. At, ex facere fugiat maxime molestiae non nostrum o'],
                ['title' => 'Тариф 4', 'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. At, ex facere fugiat maxime molestiae non nostrum o'],
                ['title' => 'Тариф 5', 'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. At, ex facere fugiat maxime molestiae non nostrum o'],
                ['title' => 'Тариф 6', 'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. At, ex facere fugiat maxime molestiae non nostrum o'],
                ['title' => 'Тариф 7', 'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. At, ex facere fugiat maxime molestiae non nostrum o'],
            ],
            'paysystems' => [
                'img/brand1.png',
                'img/brand2.png',
                'img/brand3.png',
                'img/brand4.png',
                'img/brand5.png',
                'img/brand6.png',
            ],
            'news' => [
                ['title' => 'Новость 1', 'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. At, ex facere fugiat maxime molestiae non nostrum o', 'link' => 'http://google.com.ua'],
                ['title' => 'Новость 2', 'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. At, ex facere fugiat maxime molestiae non nostrum o', 'link' => 'http://google.com.ua'],
                ['title' => 'Новость 3', 'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. At, ex facere fugiat maxime molestiae non nostrum o', 'link' => 'http://google.com.ua']
            ],
            'contacts' =>[
                'phones' => [
                    '+380661234567',
                    '+380661234567',
                    '+380661234567'
                ],
                'emails' => [
                    'email1@gmail.com',
                    'email2@gmail.com',
                    'email3@gmail.com',
                ],
                'social' => [
                    'vk' => [
                        'img' => 'img/vk', 'link' => 'http://google.com.ua'
                    ],
                    'instagram' => [
                        'img' => 'img/instagram', 'link' => 'http://google.com.ua'
                    ]
                ]
            ]
        ];
        return view('landingpage', ['data' => $data]);
    }
}
