<?php


namespace Guolei\Php\Components;


class DbFactory
{
    /**
     * get DBAL connection
     * @see https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/
     * @param array $parameters
     * @return \Doctrine\DBAL\Connection
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getDbalConnection($parameters = [])
    {
        /**
         * @see https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html#configuration
         */
        $config = new \Doctrine\DBAL\Configuration();
        return \Doctrine\DBAL\DriverManager::getConnection($parameters, $config);
    }

    /**
     * get predis connection
     * @param null $parameters
     * @param null $options
     * @return \Predis\Client
     */
    public function getPredisConnection($parameters = null, $options = null)
    {
        return new \Predis\Client($parameters, $options);
    }
}