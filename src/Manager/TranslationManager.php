<?php

namespace EffectConnect\Marketplaces\Manager;

use Exception;
use PrestaShop\PrestaShop\Core\Cache\Clearer\CacheClearerInterface;
use PrestaShopBundle\Exception\InvalidLanguageException;
use PrestaShopBundle\Service\TranslationService;
use PrestaShopBundle\Translation\DomainNormalizer;
use RuntimeException;

/**
 * Class TranslationManager
 * @package EffectConnect\Marketplaces\Manager
 */
class TranslationManager
{
    /**
     * Translation domain.
     */
    protected const TRANSLATION_DOMAIN = 'Modules.Effectconnectmarketplaces.Admin';

    /**
     * Translation source file.
     */
    protected const CSV_FILE_PATH      = __DIR__ . '/../../i18n/nl_NL.csv';

    /**
     * @var TranslationService
     */
    protected $_translationService;

    /**
     * @var CacheClearerInterface
     */
    protected $_cacheRefresh;

    /**
     * TranslationManager constructor.
     * @param TranslationService $translationService
     * @param CacheClearerInterface $cacheRefresh
     */
    public function __construct(
        TranslationService $translationService,
        CacheClearerInterface $cacheRefresh
    ) {
        $this->_translationService = $translationService;
        $this->_cacheRefresh       = $cacheRefresh;
    }

    /**
     * Import CSV translations and save them into database.
     *
     * @return bool
     */
    public function addTranslations()
    {
        // Read language file.
        try {
            $translations = $this->readCSV();
        } catch (Exception $e) {
            return false;
        }

        // For now only hardcode NL translations.
        try {
            $lang = $this->_translationService->findLanguageByLocale('nl-NL');
        } catch(InvalidLanguageException $e) {
            return false;
        }

        try {
            $domain = (new DomainNormalizer())->normalize(self::TRANSLATION_DOMAIN);
        } catch(RuntimeException $e) {
            return false;
        }

        // Add the translations from CSV file to database.
        $success = true;
        foreach ($translations as $key => $translation) {
            $result = $this->_translationService->saveTranslationMessage(
                $lang,
                $domain,
                $key,
                $translation
            );
            if ($result === false) {
                $success = false;
            }
        }

        // Refresh cache.
        try {
            $this->_cacheRefresh->clear();
        } catch (Exception $exception) {
            return false;
        }

        return $success;
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function readCSV()
    {
        $translations = [];

        $fp = fopen(self::CSV_FILE_PATH, 'r');
        if ($fp === false) {
            throw new Exception('Translation file not found');
        }

        while (($row = fgetcsv($fp, 0, ',')) !== false) {
            if (count($row) !== 2) {
                throw new Exception('Unexpected number of rows in translation file');
            }
            $translations[$row[0]] = $row[1];
        }

        fclose($fp);

        return $translations;
    }

    /**
     * @return bool
     */
    public function removeTranslations()
    {
        return true; // TODO: implement this if desirable
    }
}

