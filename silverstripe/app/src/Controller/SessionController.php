<?php

namespace App\Controller;

use SilverStripe\Control\Controller;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Security\Security;
use SilverStripe\Security\SecurityToken;

class SessionController extends Controller
{
    public function index()
    {
        /** @var Security $security */
        $security = Injector::inst()->get(Security::class);

        /** @var SecurityToken $token */
        $token = Injector::inst()->get(SecurityToken::class);
        return $token->getValue();
    }
}
