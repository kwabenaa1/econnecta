<?php

namespace ThemeXpert\Quix;

class Cache
{
    /**
     * Instance of Cache.
     *
     * @var Joomla JControllerCache wrapper
     */
    protected $cache;

    /**
     * Cache lift time.
     *
     * @var int
     */
    protected $cacheLifeTime;

    /**
     * Determine cache enable/disable.
     *
     * @var bool
     */
    private $shouldCache;

    /**
     * Create a new instance of cache.
     *
     * @param \Jfactory::getCache('quix', 'output') $cache
     * @param int                          $cacheLifeTime
     * @param bool                         $shouldCache
     */
    public function __construct($cache, $cacheLifeTime, $shouldCache)
    {
        $this->cache = $cache;

        // set caching enable/disable status
        $this->shouldCache = $shouldCache;
        $this->cache->setCaching($this->shouldCache);
        
        // set cache lifetime
        $this->cacheLifeTime = $cacheLifeTime;
        $this->cache->setLifeTime($this->cacheLifeTime);

        if (array_get($_GET, 'clear_cache')) {
            $this->clearCache();
        }

        $this->cachePath = 'lib_quix';

        // set builder type
        // if(defined("QUIX_BUILDER_TYPE")) $this->builder = QUIX_BUILDER_TYPE;
        // else $this->builder = 'frontend';

        global $QuixBuilderType;
        if(isset($QuixBuilderType)) $this->builder = $QuixBuilderType;
        else $this->builder = "frontend";
    }

    /**
     * Set cache life time.
     *
     * @param int $cacheLifeTime
     *
     * @return Application
     */
    public function setCacheLifeTime($cacheLifeTime)
    {
        $this->cacheLifeTime = $cacheLifeTime;
        $this->cache->setLifeTime($cacheLifeTime);

        return $this;
    }

    /**
     * Delete all cache from the registered cache list.
     */
    public function clearCache()
    {
        // $this->cache->deleteAll();
        $this->cache->clean('lib_quix');
    }

    /**
     * Get cache details by ID.
     *
     * @param $id
     *
     * @return mixed
     */
    public function fetch($id)
    {
        $id = $id . $this->builder;
        $args = func_get_args();

        if (count($args) === 1) {
            // return $this->cache->fetch($id);
            return $this->cache->get($id, 'lib_quix');
        } else {
            $callback = $args[1];
            return $this->getCacheById($id, $callback);
        }
    }

    /**
     * Get Cache by id.
     *
     * @param $id
     * @param $callback
     *
     * @return mixed
     */
    protected function getCacheById($id, $callback)
    {
        $id = $id . $this->builder;

        if (!$this->shouldCache) {
            return $callback();
        } else {
            $cache = $this->cache->get($id, 'lib_quix');

            if($cache){
                return $cache;
            }else{

                $data = $callback();
                // $this->cache->store($data, $id, 'quix'); // old way
                $this->cache->store($data, $id, $this->cachePath); // new path with v2 support

                return $data;
            }
            
            // if ($this->cache->contains($id)) {
            //     return $this->cache->fetch($id);
            // } else {
            //     $data = $callback();
            //     $this->cache->save($id, $data, $this->cacheLifeTime);

            //     return $data;
            // }
        }
    }
}
