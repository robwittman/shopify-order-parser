<?php

namespace App\Controller;

use App\Model\User;
use App\Model\Shop;
use App\Model\Errors;

class Users
{
    public function __construct($view, $flash)
    {
        $this->view = $view;
        $this->flash = $flash;
    }

    public function index($request, $response, $arguments)
    {
        $users = User::all();
        return $this->view->render($response, 'users/index.html', array(
            'users' => $users
        ));
    }

    public function show($request, $response, $arguments)
    {
        $uid = $arguments['id'];
        $user = User::find($uid);
        if (empty($user)) {
            $this->flash->addMessage('error', "We couldnt find user with ID of {$uid}");
            return $response->withRedirect('/users');
        }

        $this->view->render($response, 'users/show.html', array(
            'user' => $user
        ));
    }

    public function create($request, $response, $arguments)
    {
        if ($request->getAttribute('user')->role != 'admin') {
            $this->flash->addMessage('error', "Only admin can create users!");
            return $response->withRedirect('/users');
        }

        if ($request->isGet()) {
            return $this->view->render($response, 'users/new.html');
        }

        $params = $request->getParsedBody();

        if ($params['password'] !== $params['confirm']) {
            $this->flash->addMessage('error', "Passwords did not match!");
            return $response->withRedirect('/users/create');
        }

        $user = new User();
        $user->email = $params['email'];
        $user->role = $params['role'];
        $user->password = $params['password'];

        try {
            $user->save();
        } catch (\Exception $e) {
            $this->flash->addMessage('error', $e->getMessage());
            return $response->withRedirect('/users/create');
        }

        $this->flash->addMessage('message', 'User successfully created');
        return $response->withRedirect("/users/{$user->id}");
    }

    public function update($request, $response, $arguments)
    {
        if ($request->getAttribute('user')->role != 'admin') {
            $this->flash->addMessage('error', "Only admin can update users!");
            return $response->withRedirect('/users');
        }
        $user = User::find($arguments['id']);
        if (empty($user)) {
            return $this->view->render($response, 'users/index.html', array(
                'error' => "We couldn't find that user"
            ));
        }

        $params = $request->getParsedBody();
        if ($params['new_pass'] != '') {
            if ($params['new_pass'] !== $params['confirm']) {
                $this->flash->addMessage('error', "Provided passwords did not match");
                return $response->withRedirect("/users/{$arguments['id']}");
            }
            $user->password = $params['new_pass'];
        }
        $user->email = $params['email'];
        $user->role = $params['role'];
        try {
            $user->update();
        } catch (\Exception $e) {
            $this->flash->addMessage('error', $e->getMessage());
            return $response->withRedirect("/users/{$arguments['id']}");
        }

        $this->flash->addMessage('message', "User successfully updated");
        return $response->withRedirect("/users/{$arguments['id']}");
    }

    public function delete($request, $response, $arguments)
    {
        if ($request->getAttribute('user')->role != 'admin') {
            $this->flash->addMessage('error', "Only admin can delete users!");
            return $response->withRedirect('/users');
        }

        $user = User::find($arguments['id']);
        if (empty($user)) {
            $this->flash->addMessage('error', "User {$arguments['id']} not found");
            return $response->withRedirect('/users');
        }

        if ($request->isGet()) {
            return $this->view->render($response, 'users/confirm.html', array(
                'user' => $user
            ));
        } else {
            $user->delete();
            $this->flash->addMessage('message', 'User succesfully deleted');
            return $response->withRedirect('/users');
        }

    }
}
