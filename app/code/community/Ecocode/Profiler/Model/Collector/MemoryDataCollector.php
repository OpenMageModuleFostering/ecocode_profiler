<?php

/**
 * Class Ecocode_Profiler_Model_Collector_MemoryDataCollector
 */
class Ecocode_Profiler_Model_Collector_MemoryDataCollector
    extends Ecocode_Profiler_Model_Collector_AbstractDataCollector
    implements Ecocode_Profiler_Model_Collector_LateDataCollectorInterface
{
    /**
     * @return string
     */
    protected function getCurrentMemoryLimit()
    {
        return ini_get('memory_limit');
    }

    /**
     * @return int
     */
    protected function getCurrentPeakMemoryUsage()
    {
        return memory_get_peak_usage(true);
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Mage_Core_Controller_Request_Http $request, Mage_Core_Controller_Response_Http $response, \Exception $exception = null)
    {
        $this->data = [
            'memory'       => 0,
            'memory_limit' => $this->convertToBytes($this->getCurrentMemoryLimit()),
        ];

        $this->updateMemoryUsage();
    }

    /**
     * {@inheritdoc}
     */
    public function lateCollect()
    {
        $this->updateMemoryUsage();
    }

    /**
     * Gets the memory.
     *
     * @return int The memory
     */
    public function getMemory()
    {
        return $this->data['memory'];
    }

    /**
     * Gets the PHP memory limit.
     *
     * @return int The memory limit
     */
    public function getMemoryLimit()
    {
        return $this->data['memory_limit'];
    }

    /**
     * Updates the memory usage data.
     */
    public function updateMemoryUsage()
    {
        $this->data['memory'] = $this->getCurrentPeakMemoryUsage();
    }

    /**
     * @codeCoverageIgnore
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'memory';
    }

    private function convertToBytes($memoryLimit)
    {
        if ('-1' === $memoryLimit) {
            return -1;
        }

        $memoryLimit = strtolower($memoryLimit);
        $max         = strtolower(ltrim($memoryLimit, '+'));
        if (0 === strpos($max, '0x')) {
            $max = intval($max, 16);
        } elseif (0 === strpos($max, '0')) {
            $max = intval($max, 8);
        } else {
            $max = (int)$max;
        }

        switch (substr($memoryLimit, -1)) {
            case 't':
                $max *= 1024;
            case 'g':
                $max *= 1024;
            case 'm':
                $max *= 1024;
            case 'k':
                $max *= 1024;
        }

        return $max;
    }
}