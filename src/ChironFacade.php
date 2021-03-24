<?php
    /**
     * Created by PhpStorm.
     * User: JuniorE.
     * Date: 10/10/2020
     * Time: 17:52
     */

    namespace Juniore\Chiron;

    use Illuminate\Support\Facades\Facade;

    class ChironFacade extends Facade
    {
        /**
         * Get the registered name of the component.
         *
         * @return string
         */
        protected static function getFacadeAccessor()
        {
            return 'chiron';
        }
    }
