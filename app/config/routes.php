<?php

return [
    //['method' of [methods], 'route', 'controller.action [.permission]']
    ['GET',           '/', 'task.index'],
    [['GET', 'POST'], '/login', 'account.login'],
    [['GET', 'POST'], '/register', 'account.register'],

    ['GET',           '/account', 'account.index.auth'],//.auth - need login
    ['GET',           '/account/logout', 'account.logout.auth'],//.auth need login

    [['GET', 'POST'], '/task/create', 'task.create.auth'],//.auth - need login
    ['GET',           '/task/{id:\d+}', 'task.view.auth'],//need login
    ['GET',           '/task/result', 'task.result.auth'],//need login
    [['GET', 'POST'], '/task/update/{id:\d+}', 'task.update.admin'], //.admin  - need permission admin

];