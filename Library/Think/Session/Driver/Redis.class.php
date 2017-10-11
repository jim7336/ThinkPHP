<?php
namespace Think\Session\Driver;
class Redis
{
    protected $lifeTime    = 3600;
    protected $sessionName = '';
    protected $handle      = null;
    protected $prefix      = '';
    /**
     * 打开Session
     * @access public
     * @param string $savePath
     * @param mixed $sessName
     */
    public function open($savePath, $sessName)
    {
        if (!extension_loaded('redis')) {
            E(L('_NOT_SUPPORT_') . ':redis');
        }

        $options = array(
            'host'       => C('REDIS_HOST') ?: '127.0.0.1',
            'port'       => C('REDIS_PORT') ?: 6379,
            'password'   => C('REDIS_PASSWORD') ?: '',
            'timeout'    => C('DATA_CACHE_TIMEOUT') ?: false,
            'persistent' => false,
        );

        if(C('CONNECT_POOL') === true ){
            $func = "connect";
            $this->handle  = new \redis_connect_pool();
        }else{
            $func                    = $options['persistent'] ? 'pconnect' : 'connect';
            $this->handle            = new \Redis;

        }

        false === $options['timeout'] ? $this->handle->$func($options['host'], $options['port']) : $this->handle->$func($options['host'], $options['port'], $options['timeout']);
        if ('' != $options['password']) {
            $this->handle->auth($options['password']);
        }

        $this->prefix = C('SESSION_PREFIX') ? C('SESSION_PREFIX') : $this->prefix;
        $this->lifeTime = C('SESSION_EXPIRE') ? C('SESSION_EXPIRE') : $this->lifeTime;

        return true;
    }

}