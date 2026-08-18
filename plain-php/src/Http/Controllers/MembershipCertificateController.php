<?php
namespace Ishep\Http\Controllers;

use Ishep\Bootstrap\Application as App;
use Ishep\Http\{Request,Response};

final class MembershipCertificateController
{
    public function show(Request $request): Response
    {
        $app=App::instance();$membership=$app->finance()->membershipForUser((int)$app->session()->get('user_id'));
        if(!$membership||$membership['status']!=='active')return$app->render('errors/404',[],404);
        return$app->render('membership/certificate',['membership'=>$membership,'profile'=>$app->profiles()->byUserId((int)$app->session()->get('user_id'))]);
    }
}