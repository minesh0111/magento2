<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Ui\Component\Form\Element\DataType;

use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

/**
 * Class Date
 */
class Date extends AbstractDataType
{
    const NAME = 'date';

    /**
     * Current locale
     *
     * @var string
     */
    protected $locale;

    /**
     * Wrapped component
     *
     * @var UiComponentInterface
     */
    protected $wrappedComponent;

    /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param TimezoneInterface $localeDate
     * @param ResolverInterface $localeResolver
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        TimezoneInterface $localeDate,
        ResolverInterface $localeResolver,
        array $components = [],
        array $data = []
    ) {
        $this->locale = $localeResolver->getLocale();
        $this->localeDate = $localeDate;
        parent::__construct($context, $components, $data);
    }

    /**
     * Prepare component configuration
     *
     * @return void
     */
    public function prepare()
    {
        $config = $this->getData('config');

        if (!isset($config['storeTimeZone'])) {
            $storeTimeZone = $this->localeDate->getConfigTimezone();
            $config['storeTimeZone'] = $storeTimeZone;
        }
        // Set date format pattern by current locale
        $localeDateFormat = $this->localeDate->getDateFormat();
        $config['options']['dateFormat'] = $localeDateFormat;
        $config['options']['storeLocale'] = $this->locale;
        $this->setData('config', $config);
        parent::prepare();
    }

    /**
     * Get locale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Get component name
     *
     * @return string
     */
    public function getComponentName()
    {
        return static::NAME;
    }

    /**
     * Convert given date to default (UTC) timezone
     *
     * @param int $date
     * @return \DateTime|null
     */
    public function convertDate($date)
    {
        try {
            $dateObj = $this->localeDate->date(
                new \DateTime(
                    $date,
                    new \DateTimeZone($this->localeDate->getConfigTimezone())
                ),
                $this->getLocale(),
                true
            );
            //convert store date to default date in UTC timezone without DST
            $dateObj->setTimezone(new \DateTimeZone('UTC'));
            return $dateObj;
        } catch (\Exception $e) {
            return null;
        }
    }
}
