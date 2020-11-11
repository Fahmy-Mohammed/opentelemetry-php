<?php

declare(strict_types=1);

namespace OpenTelemetry\Sdk\Trace;

use ArrayAccess;
use OpenTelemetry\Trace as API;

class PropagationMap implements API\PropagationGetter, API\PropagationSetter
{
    /**
     * {@inheritdoc}
     *
     * @param array|ArrayAccess $carrier
     */
    public function get($carrier, string $key): ?string
    {
        $lKey = \strtolower($key);
        if ($carrier instanceof ArrayAccess) {
            return $carrier->offsetExists($lKey) ? $carrier->offsetGet($lKey) : null;
        }

        if (\is_array($carrier)) {
            if (empty($carrier)) {
                return null;
            }

            foreach ($carrier as $k => $value) {
                if (strtolower($k) === $lKey) {
                    return $value;
                }
            }

            return null;
        }

        throw new \InvalidArgumentException(
            sprintf('Invalid carrier of type %s. Unable to get value associated with key:%s',
            \is_object($carrier) ? \get_class($carrier) : \gettype($carrier), $key)
        );
    }

    /**
     * {@inheritdoc}
     *
     * @param array|ArrayAccess $carrier
     */
    public function set(&$carrier, string $key, string $value): void
    {
        if ($key === '') {
            throw new \InvalidArgumentException('Unable to set value with an empty key');
        }

        if ($carrier instanceof ArrayAccess || \is_array($carrier)) {
            $carrier[\strtolower($key)] = $value;

            return;
        }

        throw new \InvalidArgumentException(
            sprintf('Invalid carrier of type %s. Unable to set value',
            \is_object($carrier) ? \get_class($carrier) : \gettype($carrier), $key)
        );
    }
}