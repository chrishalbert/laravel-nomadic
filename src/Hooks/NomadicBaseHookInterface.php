<?php

namespace ChrisHalbert\LaravelNomadic\Hooks;

/**
 * Interface NomadicBaseHookInterface
 * @package ChrisHalbert\LaravelNomadic\Hooks
 */
interface NomadicBaseHookInterface
{
    /**
     * Executes a function with parameters the create receives.
     * @return string
     */
    public function execute();
}
