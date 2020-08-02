<?php
declare(strict_types=1);

namespace vyuziuk\EasyKits;

use pocketmine\plugin\PluginBase;
use vyuziuk\EasyKits\Commands\EasyKit;
use vyuziuk\EasyKits\Commands\Kit;
use vyuziuk\EasyKits\i18n\LanguageFactory;
use vyuziuk\EasyKits\Provider\AsyncLibDatabase;

class Main extends PluginBase{
    /** @var Main */
    private static $instance;

    /**
     * @return Main
     */
    public static function getInstance(): Main{
        return self::$instance;
    }

    public function onLoad(): void{
        self::$instance = $this;
    }

    public function onEnable(): void{
        $this->saveResource("config.yml", true);
        new LanguageFactory($this);
        new AsyncLibDatabase($this);
        new KitManager();
        $this->getServer()->getCommandMap()->register("easykits", new EasyKit("easykits", "", "", ["ek", "easykits"]));
        $this->getServer()->getCommandMap()->register("kit", new Kit("kit", ""));
        $this->getLogger()->info(LanguageFactory::getLanguage()->translateString('language.selected', [
            LanguageFactory::getLanguage()->translateString('language.name')
        ]));
    }

    public function onDisable(): void{
        KitManager::getInstance()->saveData();
        self::$instance = null;
    }
}