<?php

namespace NovaPoshta\Core;

class Autoload
{
    public static function init()
    {
        if (function_exists('__autoload')) { //проверяет зарегистрирована ли функция __autoload
            spl_autoload_register('__autoload'); //дает возможность создавать цепочки автозагрузки - серия функций которые могут быть вызваны для попытки загрузить класс или интерфейс.
        }

        return spl_autoload_register(array('\NovaPoshta\Core\Autoload', 'load')); //загружаем в систему функцию load
    }

    public static function load($className)
    {
        $className = str_replace('NovaPoshta\\', '', $className);
        $className = NOVA_POSHTA_PATH_SDK . $className . '.php';
        $className = str_replace('\\', DIRECTORY_SEPARATOR, $className);
		
        if ((file_exists($className) === false) || (is_readable($className) === false)) {
			return false;
        }
		
        require($className);

        return true;
    }
}
