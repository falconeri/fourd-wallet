<?php

namespace Falconeri\FourdWallet\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Falconeri\FourdWallet\Skeleton\SkeletonClass
 */
class FourdWalletFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'fourd-wallet';
    }
}
