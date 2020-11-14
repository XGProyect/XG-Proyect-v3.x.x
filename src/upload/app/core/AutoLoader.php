<?php

/**
 * This class is an auto loader for use with vanilla PHP projects' testing environment. Use it in
 * the bootstrap to register classes without having to use a framework (which you can, and should if
 * it's a better solution for you) and without having to use includes everywhere.
 *
 * It assumes that the file path in relation to the namespace follows the PSR-0 standard.
 *
 * IMPORTANT NOTE: When just registering directories, the class has no ability to discern
 * conflicting class names in different namespaces, which means that classes with the same name will
 * override each other! Always use the registerNamespace()-method if possible!
 *
 * Inspired by Jess Telford's AutoLoader (http://jes.st/).
 *
 * @see http://jes.st/2011/phpunit-bootstrap-and-autoloading-classes/
 * @see http://petermoulding.com/php/psr
 * @see http://www.php-fig.org/psr/psr-0/
 *
 * @codeCoverageIgnore
 *
 * @category    Toolbox
 * @package     Testing
 *
 * @author      Helge Söderström <helge.soderstrom@schibsted.se>
 */
class AutoLoader
{

    /**
     * An array keeping class names as key and their path as the value for classes registered with
     * AutoLoader::registerNamespace().
     *
     * @var array
     */
    protected static $namespaceClassNames = array();

    /**
     * An array keeping class names as key and their path as the value for classes registered with
     * AutoLoader::registerDirectory().
     *
     * @var array
     */
    protected static $directoryClassNames = array();

    /**
     * Store the filename (sans extension) & full path to all ".php" files found for a namespace.
     * The parameter should contain the root namespace as the key and the directory as a value.
     *
     * @param string $namespace
     * @param string $dirName
     * @return void
     */
    public static function registerNamespace($namespace, $dirName)
    {
        $directoryContents = new \DirectoryIterator($dirName);
        foreach ($directoryContents as $file) {
            if ($file->isDir() && !$file->isLink() && !$file->isDot()) {
                $newNamespace = $namespace . "_" . $file->getFileName();
                $newDirName = $dirName . "/" . $file->getFilename();
                static::registerNamespace($newNamespace, $newDirName);
            } elseif (substr($file->getFilename(), -4) === '.php') {
                $className = substr($file->getFilename(), 0, -4);
                $namespacedClassName = $namespace . "_" . $className;
                $fileName = realpath($dirName) . "/" . $file->getFilename();
                static::$namespaceClassNames[$namespacedClassName] = $fileName;
            }
        }
    }

    /**
     * Store the filename (sans extension) & full path of all ".php" files found.
     *
     * NOTE: This method will not be able to differentiate the same class names in different
     *       namespaces and will therefore overwrite class names if multiple of the same name is
     *       found. If possible, use registerNamespace instead!
     *
     * @param string $dirName
     * @return void
     */
    public static function registerDirectory($dirName)
    {
        $directoryContents = new \DirectoryIterator($dirName);
        foreach ($directoryContents as $file) {
            if ($file->isDir() && !$file->isLink() && !$file->isDot()) {
                // Recurse into directories other than a few special ones.
                static::registerDirectory($file->getPathname());
            } elseif (substr($file->getFilename(), -4) === '.php') {
                // Save the class name / path of a .php file found.
                $className = substr($file->getFilename(), 0, -4);
                AutoLoader::registerClass($className, $file->getPathname());
            }
        }
    }

    /**
     * Caches a found class with the class name as key and its path as value for use when loading
     * on the fly. The class is registered with its class name only, no namespace.
     *
     * @param string $className
     * @param string $fileName
     * @return void
     */
    public static function registerClass($className, $fileName)
    {
        AutoLoader::$directoryClassNames[$className] = $fileName;
    }

    /**
     * Includes a found class in the runtime environment. Strips namespaces.
     *
     * @param string $className
     * @return void
     */
    public static function loadClass($className)
    {
        // First, see if we've registered the entire namespace.
        $namespacedClassName = str_replace('\\', '_', $className);
        if (isset(static::$namespaceClassNames[$namespacedClassName])) {
            require_once static::$namespaceClassNames[$namespacedClassName];
            return;
        }

        // Nope. Have we registered it as a directory?
        $psrDirectorySeparators = array('\\', '_');
        foreach ($psrDirectorySeparators as $separator) {
            $separatorOccurrence = strrpos($className, $separator);
            if ($separatorOccurrence !== false) {
                $className = substr($className, $separatorOccurrence + 1);
                break;
            }
        }

        if (isset(AutoLoader::$directoryClassNames[$className])) {
            require_once AutoLoader::$directoryClassNames[$className];
        }
    }
}

// Register our AutoLoad class as the system auto loader.
spl_autoload_register(array('AutoLoader', 'loadClass'));
