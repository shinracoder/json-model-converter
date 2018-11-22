<?php

namespace HandlerPhoenix\Hydration;

/**
 * Class AbstractEntity
 *
 * @package HandlerPhoenix\Hydration
 * @author  Rob Shipley <rob.shipley@eagleeye.com>
 */
abstract class AbstractEntity
{
    /**
     * @var AbstractEntity
     */
    protected $parent;

    /**
     * AbstractEntity constructor.
     *
     * @param array|null          $params
     * @param AbstractEntity|null $parent
     */
    public function __construct(array $params = null, AbstractEntity $parent = null)
    {
        $this->parent = $parent;

        if (isset($params)) {
            $this->hydrate($params);
        }
    }

    /**
     * @param array $params
     */
    public function hydrate(array $params)
    {
        if (is_array($params)) {
            foreach ($params as $parameter => $value) {
                $method = 'set' . ucfirst($parameter);

                if ($value !== null && method_exists($this, $method)) {
                    $this->$method($value);
                }
            }
        }
    }

    /**
     * @return array
     */
    public function extract()
    {
        $returnData = [];
        foreach (get_object_vars($this) as $parameter => $value) {
            if ($parameter === 'parent') {
                continue;
            }

            if ($value instanceof AbstractEntity) {
                $value = $value->extract();
            }

            //Collection of Objects
            if (is_array($value)) {
                $value = $this->recurseThroughPayloadValues($value);
            }

            $returnData[$parameter] = $value;
        }

        unset($returnData['dateCreated']);
        unset($returnData['campaignId']);
        unset($returnData['lastUpdated']);
        unset($returnData['offerId']);

        return $returnData;
    }

    /**
     * Some entities may be a few array levels deep so we need to recurse so we don't miss them.
     *
     * @param $array
     *
     * @return mixed
     */
    public function recurseThroughPayloadValues($array)
    {
        foreach ($array as $key => $value) {
            if ($value instanceof AbstractEntity) {
                $array[$key] = $value->extract();
            } elseif (is_array($value)) {
                $array[$key] = $this->recurseThroughPayloadValues($value);
            } else {
                $array[$key] = $value;
            }
        }

        return $array;
    }

    /**
     * @return AbstractEntity|null
     */
    protected function getParent()
    {
        return $this->parent;
    }

    /**
     * @return $this|AbstractEntity
     */
    protected function getTopParent()
    {
        $parent = $this->getParent();

        if (!is_null($parent)) {
            return $parent->getTopParent();
        }

        return $this;
    }
}
