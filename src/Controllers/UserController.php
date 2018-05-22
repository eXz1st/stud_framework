<?php
namespace Mindk\Framework\Controllers;
use Mindk\Framework\Exceptions\AuthRequiredException;
use Mindk\Framework\Http\Request\Request;
use Mindk\Framework\Models\UserModel;
/**
 * Class UserController
 * @package Mindk\Framework\Controllers
 */
class UserController
{
    public function register(Request $request, UserModel $model) {
        $data = array();
        $data['email'] = $request->get('login', '', 'string');
        $data['password'] =  md5($request->get('password', ''));
        $data['token'] = md5(uniqid());
        $data['role'] = $request->get('role', '', 'string');

        $model->create($data);

        return $data['token'];
    }
    /**
     * Login through action
     *
     * @param Request $request
     * @param UserModel $model
     *
     * @return mixed
     * @throws AuthRequiredException
     */
    public function login(Request $request, UserModel $model) {
        $resp = array();
        if($login = $request->get('login', '', 'string')) {
            $user = $model->findByCredentials($login, $request->get('password', ''));
        }

        if(empty($user)) {
            throw new AuthRequiredException('Bad access credentials provided');
        }

        $user->token = md5(uniqid());
        $user->save();

        $resp['id'] = $user->id;
        $resp['token'] = $user->token;
        return $resp;
    }
    public function logout(Request $request, UserModel $model) {
        $user = $model->findByToken($request->get('token', '');

        if(empty($user)) {
            throw new AuthRequiredException('Bad access credentials provided');
        }

        $user->token = md5(uniqid());

        return $user->save();
    }
}