<?php

namespace App\Helpers\Traits;


use App\User;

class GetName
{
    /**
     * @param $email
     *
     * @return mixed
     */
    public static function getName($id)
    {
        $user = User::where('id', $id)->first();
        return $user->name;
    }
}
