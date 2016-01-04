<?php

namespace Adrianyg7\Acl\Http\Middleware;

use Closure;
use Adrianyg7\Acl\Policies\AclPolicy;

class Acl
{
    /**
     * The Acl Policy.
     *
     * @var \App\Policies\AclPolicy
     */
    protected $aclPolicy;

    /**
     * Constructor
     */
    public function __construct(AclPolicy $aclPolicy)
    {
        $this->aclPolicy = $aclPolicy;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->aclPolicy->defineAbilities();

        if ( ! $this->aclPolicy->passesAuthorization() ) {
            abort(403, config('acl.unauthorized_message'));
        }

        return $next($request);
    }

}
