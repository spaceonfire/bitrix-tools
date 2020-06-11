<?php

use Composer\Autoload\ClassLoader;
use function Composer\Autoload\includeFile;

if (file_exists($composerAutoloaderPath = __DIR__ . '/../../../autoload.php')) {
    $patchedLoader = new class(require $composerAutoloaderPath) {
        private $loader;

        public function __construct(ClassLoader $loader)
        {
            $loader->unregister();
            $this->loader = $loader;
        }

        public function register($prepend = false): void
        {
            spl_autoload_register([$this, 'loadClass'], true, $prepend);
        }

        public function unregister(): void
        {
            spl_autoload_unregister([$this, 'loadClass']);
        }

        /**
         * Loads the given class or interface.
         * @param string $class The name of the class
         * @return bool True if loaded, null otherwise
         */
        public function loadClass($class): bool
        {
            /**
             * Патчим композеровский лоадер.
             *
             * Из-за того что битриксовый лоадер всегда конвертирует имя файла в нижний регистр
             * в регистронезависимых файловых системах может возникать баг повторного подключения
             * файла разными лоадерами. PHP воспринимает имя файла в разных регистрах как разные файлы.
             *
             * Чтобы избежать этого, по возможности всегда подключаем файл в нижнем регистре.
             *
             * @see \Bitrix\Main\Loader::autoLoad()
             */

            if ($file = $this->loader->findFile($class)) {
                $lowercaseFile = strtolower($file);
                includeFile(file_exists($lowercaseFile) ? $lowercaseFile : $file);
                return true;
            }

            return false;
        }
    };

    $patchedLoader->register(true);
}
