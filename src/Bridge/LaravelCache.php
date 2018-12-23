<?php
/**
 * Created by zed.
 */
declare(strict_types=1);

namespace Dezsidog\YzSdk\Bridge;


use Illuminate\Cache\RedisTaggedCache;
use Illuminate\Cache\Repository;
use Illuminate\Cache\TaggableStore;
use Illuminate\Cache\TaggedCache;
use Illuminate\Contracts\Cache\Store;
use Psr\SimpleCache\CacheInterface;

class LaravelCache implements CacheInterface
{
    /**
     * @var Repository|TaggedCache|RedisTaggedCache
     */
    protected $laravelStore;

    public function __construct(Store $cache)
    {
        $this->laravelStore = $cache;
    }

    public function isTagable()
    {
        return $this->laravelStore instanceof TaggableStore;
    }

    protected function parseTagAndKey($str): array
    {
        $items = explode($str, '_');
        $tag = array_slice($items, 0, 3);
        $key = array_slice($items, 3);
        return [$tag, $key];
    }

    /**
     * Fetches a value from the cache.
     *
     * @param string $key The unique key of this item in the cache.
     * @param mixed $default Default value to return if the key does not exist.
     *
     * @return mixed The value of the item from the cache, or $default in case of cache miss.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *   MUST be thrown if the $key string is not a legal value.
     */
    public function get($key, $default = null)
    {
        if ($this->isTagable()) {
            [$tag, $key] = $this->parseTagAndKey($key);
            $value = $this->laravelStore->tags($tag)->get($key);
        } else {
            $value = $this->get($key);
        }

        if (is_null($value)) {
            return $default;
        } else {
            return $value;
        }
    }

    /**
     * Persists data in the cache, uniquely referenced by a key with an optional expiration TTL time.
     *
     * @param string $key The key of the item to store.
     * @param mixed $value The value of the item to store, must be serializable.
     * @param null|int|\DateInterval $ttl Optional. The TTL value of this item. If no value is sent and
     *                                      the driver supports TTL then the library may set a default value
     *                                      for it or let the driver take care of that.
     *
     * @return bool True on success and false on failure.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *   MUST be thrown if the $key string is not a legal value.
     */
    public function set($key, $value, $ttl = null): bool
    {
        if ($this->isTagable()) {
            [$tag, $key] = $this->parseTagAndKey($key);
            return $this->laravelStore->tags($tag)->set($key, $value, $ttl);
        } else {
            return $this->laravelStore->set($key, $value, $ttl);
        }
    }

    /**
     * Delete an item from the cache by its unique key.
     *
     * @param string $key The unique cache key of the item to delete.
     *
     * @return bool True if the item was successfully removed. False if there was an error.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *   MUST be thrown if the $key string is not a legal value.
     */
    public function delete($key)
    {
        if ($this->isTagable()) {
            [$tag, $key] = $this->parseTagAndKey($key);
            return $this->laravelStore->tags(implode('_', $tag))->delete(implode('_', $key));
        } else {
            return $this->laravelStore->delete($key);
        }
    }

    /**
     * Wipes clean the entire cache's keys.
     *
     * @return bool True on success and false on failure.
     */
    public function clear(): bool
    {
        return $this->laravelStore->clear();
    }

    /**
     * Obtains multiple cache items by their unique keys.
     *
     * @param iterable $keys A list of keys that can obtained in a single operation.
     * @param mixed $default Default value to return for keys that do not exist.
     *
     * @return iterable A list of key => value pairs. Cache keys that do not exist or are stale will have $default as value.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *   MUST be thrown if $keys is neither an array nor a Traversable,
     *   or if any of the $keys are not a legal value.
     */
    public function getMultiple($keys, $default = null): array
    {
        $values = [];
        foreach ($keys as $key) {
            if (is_array($default) && isset($default[$key])) {
                $values[$key] = $this->get($key, $default[$key]);
            } else {
                $values[$key] = $this->get($key);
            }
        }

        if (is_array($default)) {
            foreach ($default as $defaultKey => $defaultValue) {
                if (!in_array($defaultKey, $values)) {
                    $values[$defaultKey] = $defaultValue;
                }
            }
        }

        return $values;
    }

    /**
     * Persists a set of key => value pairs in the cache, with an optional TTL.
     *
     * @param iterable $values A list of key => value pairs for a multiple-set operation.
     * @param null|int|\DateInterval $ttl Optional. The TTL value of this item. If no value is sent and
     *                                       the driver supports TTL then the library may set a default value
     *                                       for it or let the driver take care of that.
     *
     * @return bool True on success and false on failure.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *   MUST be thrown if $values is neither an array nor a Traversable,
     *   or if any of the $values are not a legal value.
     */
    public function setMultiple($values, $ttl = null): bool
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value, $ttl);
        }

        return true;
    }

    /**
     * Deletes multiple cache items in a single operation.
     *
     * @param iterable $keys A list of string-based keys to be deleted.
     *
     * @return bool True if the items were successfully removed. False if there was an error.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *   MUST be thrown if $keys is neither an array nor a Traversable,
     *   or if any of the $keys are not a legal value.
     */
    public function deleteMultiple($keys)
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }
        return true;
    }

    /**
     * Determines whether an item is present in the cache.
     *
     * NOTE: It is recommended that has() is only to be used for cache warming type purposes
     * and not to be used within your live applications operations for get/set, as this method
     * is subject to a race condition where your has() will return true and immediately after,
     * another script can remove it making the state of your app out of date.
     *
     * @param string $key The cache item key.
     *
     * @return bool
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *   MUST be thrown if the $key string is not a legal value.
     */
    public function has($key)
    {
        if ($this->isTagable()) {
            [$tag, $key] = $this->parseTagAndKey($key);
            return $this->laravelStore->tags($tag)->has($key);
        } else {
            return $this->has($key);
        }
    }
}