<?php

namespace core;
/**
 * Class IOC
 * @package core
 * 依赖注入
 */
class IOC
{
    public static $dependence;

    /**
     * @param $className
     * @return object
     * 获取类的实例
     */
    public static function getInstance($className)
    {
        return (new \ReflectionClass($className))->newInstanceArgs(self::getMethodParams($className));
    }

    /**
     * @param $className
     * @param $dependence
     * 动态设置依赖关系
     */
    public static function set($className, $dependence)
    {
        self::$dependence[$className] = $dependence;
    }

    /**
     * @param $className
     * @param string $methodName
     * @return array
     * 获取构造函数参数
     */
    protected static function getMethodParams($className, $methodName = '__construct')
    {
        $paramsArr = [];
        if (isset(self::$dependence[$className])) { //如果设置了依赖关系
            $dependent = self::$dependence[$className];
            if (is_string($dependent)) {      // 依赖是字符串
                if (class_exists($dependent)) {    //该字符串是类
                    $paramsArr[] = self::getInstance($dependent);
                } else {                          //该字符串是普通参数
                    $paramsArr[] = $dependent;
                }
            }
            if (is_array($dependent)) {     // 依赖是数组
                $class = new \ReflectionClass($className);
                if ($class->hasMethod($methodName)) {
                    $method = $class->getMethod($methodName);
                    $params = $method->getParameters();
                    if (count($params) > 0) {
                        foreach ($params as $key => $param) {
                            $paramName = $param->getName();
                            if (isset($dependent[$paramName])) {
                                $paramValue = $dependent[$paramName];
                                if (is_string($paramValue) && class_exists($paramValue) && $param->getClass()) { //
                                    $paramsArr[] = self::getInstance($paramValue);
                                } else {
                                    $paramsArr[] = $paramValue;
                                }
                            } else {
                                break;
                            }
                        }
                    }
                }
            }
        } else { // 如果没有设置依赖，跟根据构造函数参数自动创建，只支持类参数且指明类型
            $class = new \ReflectionClass($className);
            if ($class->hasMethod($methodName)) {
                $method = $class->getMethod($methodName);
                $params = $method->getParameters();
                if (count($params) > 0) {
                    foreach ($params as $key => $param) {
                        if ($paramClass = $param->getClass()) {
                            $paramClassName = $paramClass->getName();
                            $paramsArr[] = self::getInstance($paramClassName);
                        }
                    }
                }
            }
        }
        return $paramsArr;
    }
}