<?php

namespace App\Core;

use App\Core\Exception as Exception;
use \ReflectionClass as ReflectionClass;
use \Closure as Closure;

class Container
{

    public static $registry = [];

    public static function bind($className, Callable $resolver)
    {
        static::$registry[$className] = $resolver;
    }

    public static function make($className)
    {
        if (isset(static::$registry[$className])) {
            $resolver = static::$registry[$className];
            if ($resolver instanceof Closure) {
                // 执行闭包函数，并将结果
                return $resolver();
            }
            return $resolver;
        } elseif (is_string($className)) {
            return static::build($className);
        }
        throw new Exception('Alias does not exist in the IoC registry.');
    }

    /**
     * 自动绑定（Autowiring）自动解析（Automatic Resolution）
     *
     * @param string $className
     *
     * @return object
     * @throws Exception
     */
    public static function build($className)
    {
        /** @var ReflectionClass $reflector */
        $reflector = new ReflectionClass($className);

        // 检查类是否可实例化, 排除抽象类abstract和对象接口interface
        if (!$reflector->isInstantiable()) {
            throw new Exception("Can't instantiate this.");
        }

        /** @var ReflectionMethod $constructor 获取类的构造函数 */
        $constructor = $reflector->getConstructor();

        // 若无构造函数，直接实例化并返回
        if (is_null($constructor)) {
            static::$registry[$className] = new $className;
            return static::$registry[$className];
        }

        // 取构造函数参数,通过 ReflectionParameter 数组返回参数列表
        $parameters = $constructor->getParameters();

        // 递归解析构造函数的参数
        $dependencies = static::getDependencies($parameters);

        // 创建一个类的新实例，给出的参数将传递到类的构造函数。

        $resolver = $reflector->newInstanceArgs($dependencies);
        static::$registry[$className] = $resolver;

        return static::$registry[$className];
    }

    /**
     * @param array $parameters
     *
     * @return array
     * @throws Exception
     */
    public static function getDependencies($parameters)
    {
        $dependencies = [];

        /** @var ReflectionParameter $parameter */
        foreach ($parameters as $parameter) {
            /** @var ReflectionClass $dependency */
            $dependency = $parameter->getClass();

            if (is_null($dependency)) {
                // 是变量,有默认值则设置默认值
                $dependencies[] = static::resolveNonClass($parameter);
            } else {
                // 是一个类，递归解析
                $dependencies[] = static::build($dependency->name);
            }
        }

        return $dependencies;
    }

    /**
     * @param ReflectionParameter $parameter
     *
     * @return mixed
     * @throws Exception
     */
    public static function resolveNonClass($parameter)
    {
        // 有默认值则返回默认值
        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        throw new Exception('I have no idea what to do here.');
    }
}
