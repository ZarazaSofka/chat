<?php

$router = new Router();

$router->addRoute("/", "ChatsController@getChats@GET@USER|ADMIN");
$router->addRoute("/chat/(\d+)", "ChatsController@getChat@GET@USER|ADMIN");
$router->addRoute("/chat/profile/(\d+)", "ChatsController@getChatProfile@GET@USER|ADMIN");

$router->addRoute("/api/chat/join/(\d+)", "ChatsController@joinChat@POST@USER|ADMIN");
$router->addRoute("/api/chat/create", "ChatsController@createChat@POST@USER|ADMIN");
$router->addRoute("/api/chat/profile/(\d+)", "ChatsController@getChatData@GET@USER|ADMIN");
$router->addRoute("/api/chat/send/(\d+)", "ChatsController@sendMessage@POST@USER|ADMIN");
$router->addRoute("/api/chat/messages/(\d+)", "ChatsController@getMessages@GET@USER|ADMIN");
$router->addRoute("/api/chat/update/(\d+)", "ChatsController@updateChat@POST@USER|ADMIN");
$router->addRoute("/api/chat/delete/(\d+)", "ChatsController@deleteChat@POST@USER|ADMIN");
$router->addRoute("/api/chat/leave/(\d+)", "ChatsController@leaveChat@POST@USER|ADMIN");
$router->addRoute("/api/chat/changerole/(\d+)", "ChatsController@changeRole@POST@USER|ADMIN");

$router->addRoute("/api/chats/public", "ChatsController@getPublicChats@GET@USER|ADMIN");
$router->addRoute("/api/chats/user", "ChatsController@getUserChats@GET@USER|ADMIN");

$router->addRoute("/register", "UserController@registerPage@GET@ANON");
$router->addRoute("/api/user/register", "UserController@register@POST@ANON");
$router->addRoute("/login", "UserController@loginPage@GET@ANON");
$router->addRoute("/api/user/login", "UserController@login@POST@ANON");
$router->addRoute("/logout", "UserController@logout@GET@USER|ADMIN");

$router->addRoute("/users", "UserController@usersPage@GET@ADMIN");
$router->addRoute("/api/users/get", "UserController@getUsers@GET@ADMIN");
$router->addRoute("/api/users/delete", "UserController@deleteUser@POST@ADMIN");