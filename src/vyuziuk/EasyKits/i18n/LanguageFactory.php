<?php
declare(strict_types=1);

namespace vyuziuk\EasyKits\i18n;

use vyuziuk\EasyKits\Main;

class LanguageFactory{
    /** @var LanguageFactory */
    private static $instance;

    const DIRECTORY = "languages";
    const FALLBACK = "en_us";//default language, if not selected in the config.yml

    /** @var Language */
    private static $language;

    private static $languages = [
        "en_us",
        "ru_ru"
    ];

    public function __construct(Main $main){
        self::$instance = $this;
        foreach(self::getLanguages() as $language){
            $main->saveResource(self::DIRECTORY . DIRECTORY_SEPARATOR . $language . ".ini", true);
        }
        if(!in_array($main->getConfig()->get("language"), self::$languages)){
            self::$language = new Language(self::FALLBACK);
            $main->getLogger()->alert(self::getLanguage()->translateString("language.not.found", [
                $main->getConfig()->get("language")
            ]));
        }else{
            self::$language = new Language($main->getConfig()->get("language"));
        }
    }

    /**
     * @return string[]
     */
    public static function getLanguages(): array{
        return self::$languages;
    }

    /**
     * @return Language
     */
    public static function getLanguage(): Language{
        return self::$language;
    }

    /**
     * @return LanguageFactory
     */
    public static function getInstance(): LanguageFactory{
        return self::$instance;
    }
}